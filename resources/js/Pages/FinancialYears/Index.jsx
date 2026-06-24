import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, router } from '@inertiajs/react';
import { resourceData } from '@/utils/resource';

export default function Index({ financialYears, filters, isAllYearsMode }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const list = financialYears?.data ?? [];

    const handleClose = (id) => {
        if (confirm(t('financial_years.confirm_close'))) {
            router.post(route('financial-years.close', id));
        }
    };

    const handleActivate = (year) => {
        const message = year.is_all_years
            ? t('financial_years.confirm_activate_all')
            : t('financial_years.confirm_activate').replace(':name', year.display_name || year.name);

        if (confirm(message)) {
            router.post(route('financial-years.activate', year.id));
        }
    };

    const yearLabel = (year) => year.display_name || year.name;

    return (
        <ErpLayout>
            <Head title={t('financial_years.title')} />
            <FlashMessage />
            <PageHeader
                title={t('financial_years.title')}
                createRoute={can('financial-year.create') ? route('financial-years.create') : null}
                createLabel={t('financial_years.create')}
            />

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
                            <tr key={year.id} className={year.is_all_years ? 'bg-slate-50' : ''}>
                                <td className="px-4 py-3 font-medium">{yearLabel(year)}</td>
                                <td className="px-4 py-3">{year.is_all_years ? '—' : year.start_date_shamsi}</td>
                                <td className="px-4 py-3">{year.is_all_years ? '—' : year.end_date_shamsi}</td>
                                <td className="px-4 py-3">
                                    {year.status === 'active' && year.is_all_years
                                        ? t('financial_years.all_years_mode')
                                        : t(`financial_years.statuses.${year.status}`)}
                                </td>
                                <td className="px-4 py-3 text-right">
                                    <div className="inline-flex items-center gap-2">
                                        {!year.is_all_years && (
                                            <ActionButtons
                                                viewHref={route('financial-years.show', year.id)}
                                                editHref={year.status === 'active' && can('financial-year.edit') ? route('financial-years.edit', year.id) : null}
                                            />
                                        )}
                                        {year.status !== 'active' && can('financial-year.activate') && (
                                            <button
                                                type="button"
                                                onClick={() => handleActivate(year)}
                                                className="rounded-md border border-indigo-300 px-2 py-1 text-xs text-indigo-800 hover:bg-indigo-50"
                                            >
                                                {year.is_all_years ? t('financial_years.all_years_name') : t('financial_years.activate')}
                                            </button>
                                        )}
                                        {year.status === 'active' && !year.is_all_years && can('financial-year.close') && (
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
