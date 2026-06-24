import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ entries, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <ErpLayout>
            <Head title={t('accounting.title')} />
            <FlashMessage />
            <PageHeader title={t('accounting.title')} createRoute={can('accounting.create') ? route('journal-entries.create') : null} createLabel={t('accounting.create')}>
                <Link href={route('accounting.trial-balance')} className="text-sm text-indigo-600 hover:underline">{t('accounting.trial_balance')}</Link>
            </PageHeader>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.entry_date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.reference')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {entries?.data?.length === 0 && (
                            <tr><td colSpan="4" className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {entries?.data?.map((e) => (
                            <tr key={e.id}>
                                <td className="px-4 py-3">{e.entry_date_shamsi}</td>
                                <td className="px-4 py-3">{e.reference}</td>
                                <td className="px-4 py-3">{e.description}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('journal-entries.show', e.id)}
                                        editHref={can('accounting.edit') && !e.is_locked ? route('journal-entries.edit', e.id) : null}
                                        onDelete={can('accounting.delete') && !e.is_locked ? () => router.delete(route('journal-entries.destroy', e.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={entries} /></div>
            </div>
        </ErpLayout>
    );
}
