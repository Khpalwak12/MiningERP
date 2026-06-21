import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head } from '@inertiajs/react';

export default function Show({ user }) {
    const { t } = useTranslation();
    const u = user.data;

    return (
        <ErpLayout>
            <Head title={t('users.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{u.name}</h1>
                <ActionButtons editHref={route('users.edit', u.id)} />
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.email')}</dt><dd>{u.email}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.locale')}</dt><dd>{u.locale}</dd></div>
                    <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.roles')}</dt><dd>{u.roles?.join(', ')}</dd></div>
                </dl>
            </div>
        </ErpLayout>
    );
}
