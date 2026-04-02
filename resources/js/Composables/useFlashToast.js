import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export function useFlashToast() {
    const page = usePage();

    watch(() => page.props.flash?.success, (message) => {
        if (message) {
            toast.success(message);
        }
    });

    watch(() => page.props.flash?.error, (message) => {
        if (message) {
            toast.error(message);
        }
    });
}
