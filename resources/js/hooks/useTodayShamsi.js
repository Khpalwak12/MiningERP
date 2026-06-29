import { usePage } from '@inertiajs/react';

export default function useTodayShamsi() {
    return usePage().props.todayShamsi ?? '';
}
