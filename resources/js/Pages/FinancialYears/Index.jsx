import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, router } from '@inertiajs/react';
import { resourceData } from '@/utils/resource';

export default function Index({ financialYears, filters, activeYear }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const list = financialYears?.data ?? [];
    const active = resourceData(activeYear);

    const handleClose = (id) => {
        if (confirm(t('financial_years.confirm_close'))) {
            router.post(route('financial-years.close', id));
        }
    };

    const handleActivate = (year) => {
        const message = t('financial_years.confirm_activate').replace(':name', year.name);
        if (confirm(message)) {
            router.post(route('financial-years.activate', year.id));
        }
    };

    return (
        <ErpLayout>
            <Head title={t('financial_years.title')} />
            <FlashMessage />
            <PageHeader
                title={t('financial_years.title')}
                createRoute={can('financial-year.create') ? route('financial-years.create') : null}
                createLabel={t('financial_years.create')}
            />

            {active?.name && (
                <div className="mb-4 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-900">
                    {t('financial_years.active_label')}: <strong>{active.name}</strong>
                </div>
            )}

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.year_name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.start_date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.end_date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={5} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((year) => (
                            <tr key={year.id}>
                                <td className="px-4 py-3 font-medium">{year.name}</td>
                                <td className="px-4 py-3">{year.start_date_shamsi}</td>
                                <td className="px-4 py-3">{year.end_date_shamsi}</td>
                                <td className="px-4 py-3">{t(`financial_years.statuses.${year.status}`)}</td>
                                <td className="px-4 py-3 text-right">
                                    <div className="inline-flex items-center gap-2">
                                        <ActionButtons
                                            viewHref={route('financial-years.show', year.id)}
                                            editHref={year.status === 'active' && can('financial-year.edit') ? route('financial-years.edit', year.id) : null}
                                        />
                                        {year.status !== 'active' && can('financial-year.activate') && (
                                            <button
                                                type="button"
                                                onClick={() => handleActivate(year)}
                                                className="rounded-md border border-indigo-300 px-2 py-1 text-xs text-indigo-800 hover:bg-indigo-50"
                                            >
                                                {t('financial_years.activate')}
                                            </button>
                                        )}
                                        {year.status === 'active' && can('financial-year.close') && (
                                            <button
                                                type="button"
                                                onClick={() => handleClose(year.id)}
                                                className="rounded-md border border-amber-300 px-2 py-1 text-xs text-amber-800 hover:bg-amber-50"
                                            >
                                                {t('financial_years.close')}
                                            </button>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={financialYears} /></div>
            </div>
        </ErpLayout>
    );
}
