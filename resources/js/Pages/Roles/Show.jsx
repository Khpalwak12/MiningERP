import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head } from '@inertiajs/react';

export default function Show({ role }) {
    const { t } = useTranslation();
    const r = role.data;

    return (
        <ErpLayout>
            <Head title={t('roles.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{r.name}</h1>
                <ActionButtons editHref={route('roles.edit', r.id)} />
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <h2 className="mb-3 font-semibold">{t('fields.permissions')}</h2>
                <ul className="grid gap-1 sm:grid-cols-2">
                    {(r.permissions || []).map((perm) => (
                        <li key={perm} className="text-gray-700">{perm}</li>
                    ))}
                </ul>
            </div>
        </ErpLayout>
    );
}
