import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function ProfitLoss({ report, filters }) {
    const { t } = useTranslation();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');

    const items = [
        { key: 'marble_sales', label: t('reports.marble_sales') },
        { key: 'sankari_sales', label: t('reports.sankari_sales') },
        { key: 'total_income', label: t('reports.total_income') },
        { key: 'operating_expenses', label: t('reports.operating_expenses') },
        { key: 'payroll_expenses', label: t('reports.payroll_expenses') },
        { key: 'total_expenses', label: t('reports.total_expenses') },
        { key: 'net_profit', label: t('reports.net_profit') },
    ];

    return (
        <ErpLayout>
            <Head title={t('reports.profit_loss')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.profit_loss')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <form onSubmit={(e) => { e.preventDefault(); router.get(route('reports.profit-loss'), { date_from: dateFrom, date_to: dateTo }, { preserveState: true }); }} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
            </form>
            <div className="rounded-lg bg-white p-6 shadow">
                <table className="min-w-full text-sm">
                    <tbody className="divide-y">
                        {items.map((item) => (
                            <tr key={item.key} className={item.key === 'net_profit' ? 'font-bold' : ''}>
                                <td className="py-3">{item.label}</td>
                                <td className="py-3 text-right">{formatCurrency(report?.[item.key] ?? 0)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
