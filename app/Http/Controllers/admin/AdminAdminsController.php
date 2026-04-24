<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\ClassParticipant;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminAdminsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/administrateurs",
     *     tags={"Admin - Administrateurs"},
     *     summary="Liste des co-administrateurs des cours",
     *     operationId="admin.admins.index",
     *     security={{"session":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Props Inertia de la page administrateurs",
     *         @OA\JsonContent(
     *             @OA\Property(property="admins", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="userId", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="avatar", type="string", nullable=true),
     *                 @OA\Property(property="isPlayer", type="boolean"),
     *                 @OA\Property(property="createdAt", type="string"),
     *                 @OA\Property(property="classes", type="array", @OA\Items(type="object"))
     *             )),
     *             @OA\Property(property="adminCount", type="integer"),
     *             @OA\Property(property="currentUserId", type="string", format="uuid")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Accès réservé aux admins purs (non-joueurs)")
     * )
     */
    public function index(): Response
    {
        $user = auth('admin')->user();

        if ($user->player) {
            abort(403);
        }

        $adminUser = $user->adminUser;

        $classIds = $adminUser->classParticipants()
            ->pluck('school_class_id');

        $adminParticipants = ClassParticipant::whereIn('school_class_id', $classIds)
            ->where('participantable_type', AdminUser::class)
            ->with(['participantable.user', 'schoolClass'])
            ->get();

        $adminsGrouped = $adminParticipants
            ->groupBy(fn ($cp) => $cp->participantable->user_id)
            ->map(function ($group) {
                $first = $group->first();
                $adminModel = $first->participantable;
                $userModel = $adminModel->user;

                $isAlsoPlayer = $userModel->player !== null;

                return [
                    'id'        => $adminModel->id,
                    'userId'    => $userModel->id,
                    'name'      => $userModel->full_name,
                    'email'     => $userModel->email,
                    'avatar'    => $userModel->profile_picture,
                    'isPlayer'  => $isAlsoPlayer,
                    'createdAt' => $adminModel->created_at->format('d/m/Y'),
                    'classes'   => $group->map(fn ($cp) => [
                        'id'   => $cp->schoolClass->id,
                        'name' => $cp->schoolClass->name,
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();

        return Inertia::render('Admin/Admins', [
            'admins'        => $adminsGrouped,
            'adminCount'    => count($adminsGrouped),
            'currentUserId' => $user->id,
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/admin/administrateurs/{admin}",
     *     tags={"Admin - Administrateurs"},
     *     summary="Retirer un administrateur",
     *     operationId="admin.admins.destroy",
     *     security={{"session":{}}},
     *     @OA\Parameter(name="admin", in="path", required=true, description="UUID de l'AdminUser", @OA\Schema(type="string", format="uuid")),
     *     @OA\Response(response=302, description="Retour avec message de succès"),
     *     @OA\Response(response=422, description="Impossible de se retirer soi-même")
     * )
     */
    public function destroy(AdminUser $admin): RedirectResponse
    {
        $currentUser = auth('admin')->user();

        if ($currentUser->player) {
            abort(403);
        }

        if ($admin->user_id === $currentUser->id) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous retirer vous-même.']);
        }

        ClassParticipant::where('participantable_type', AdminUser::class)
            ->where('participantable_id', $admin->id)
            ->delete();

        $admin->delete();

        return back()->with('success', 'Administrateur retiré avec succès.');
    }
}
