import { useLayoutEffect } from 'react';
import { usePage } from '@inertiajs/react';
import { syncDocumentDirection } from '@/utils/direction';

export default function useSyncDocumentDirection() {
    const locale = usePage().props.locale ?? 'en';

    useLayoutEffect(() => {
        syncDocumentDirection(locale);
    }, [locale]);
}
