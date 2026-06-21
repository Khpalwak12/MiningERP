import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import useTranslation from '@/hooks/useTranslation';

export default function FlashMessage() {
    const { flash } = usePage().props;
    const { t } = useTranslation();

    useEffect(() => {
        if (flash?.success || flash?.error) {
            const timer = setTimeout(() => {}, 5000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    if (!flash?.success && !flash?.error) return null;

    return (
        <div className="mb-4">
            {flash.success && (
                <div className="rounded-md bg-green-50 p-4 text-sm text-green-800 border border-green-200">
                    {flash.success}
                </div>
            )}
            {flash.error && (
                <div className="rounded-md bg-red-50 p-4 text-sm text-red-800 border border-red-200">
                    {flash.error}
                </div>
            )}
        </div>
    );
}
