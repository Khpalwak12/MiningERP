import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import FinancialYearSelect from '@/Components/Erp/FinancialYearSelect';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function CustomerBalances({ customers, filters, financialYears }) {
    const { t } = useTranslation();
    const [financialYearId, setFinancialYearId] = useState(filters?.financial_year_id || '');
    const list = customers?.data ?? [];

    return (
        <ErpLayout>
            <Head title={t('reports.customer_balances')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.customer_balances')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <form onSubmit={(e) => { e.preventDefault(); router.get(route('reports.customer-balances'), { financial_year_id: financialYearId }, { preserveState: true }); }} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <FinancialYearSelect value={financialYearId} onChange={setFinancialYearId} financialYears={financialYears} />
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
            </form>
            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.customer')}</th>
                            <th className="px-4 py-3 text-left">{t('reports.total_sales')}</th>
                            <th className="px-4 py-3 text-left">{t('reports.total_payments')}</th>
                            <th className="px-4 py-3 text-left">{t('reports.outstanding_balance')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.map((c) => (
                            <tr key={c.id}>
                                <td className="px-4 py-3">{c.name}</td>
                                <td className="px-4 py-3">{formatCurrency(c.total_sales)}</td>
                                <td className="px-4 py-3">{formatCurrency(c.total_payments)}</td>
                                <td className="px-4 py-3 font-medium text-amber-700">{formatCurrency(c.outstanding_balance)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
