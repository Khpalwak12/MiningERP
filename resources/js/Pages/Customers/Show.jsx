import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head } from '@inertiajs/react';

export default function Show({ customer, ledger }) {
    const { t } = useTranslation();
    const c = customer.data;

    return (
        <ErpLayout>
            <Head title={c.name} />
            <FlashMessage />

            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{c.name}</h1>
                <ActionButtons editHref={route('customers.edit', c.id)} />
            </div>

            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <div className="rounded-lg bg-white p-4 shadow">
                    <p className="text-sm text-gray-500">{t('customers.total_sales')}</p>
                    <p className="text-xl font-bold">{formatCurrency(c.total_sales)}</p>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <p className="text-sm text-gray-500">{t('customers.total_payments')}</p>
                    <p className="text-xl font-bold">{formatCurrency(c.total_payments)}</p>
                </div>
                <div className="rounded-lg bg-white p-4 shadow">
                    <p className="text-sm text-gray-500">{t('customers.outstanding_balance')}</p>
                    <p className="text-xl font-bold text-amber-700">{formatCurrency(c.outstanding_balance)}</p>
                </div>
            </div>

            <div className="mb-6 rounded-lg bg-white p-4 shadow">
                <h2 className="mb-3 font-semibold">{t('customers.show')}</h2>
                <dl className="grid gap-2 sm:grid-cols-2 text-sm">
                    <div><dt className="text-gray-500">{t('fields.owner_name')}</dt><dd>{c.owner_name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.phone')}</dt><dd>{c.phone}</dd></div>
                    <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.address')}</dt><dd>{c.address}</dd></div>
                </dl>
            </div>

            <div className="rounded-lg bg-white p-4 shadow">
                <h2 className="mb-3 font-semibold">Ledger</h2>
                <table className="min-w-full text-sm">
                    <thead>
                        <tr className="border-b text-left text-gray-500">
                            <th className="py-2">{t('fields.date')}</th>
                            <th className="py-2">Type</th>
                            <th className="py-2">{t('fields.description')}</th>
                            <th className="py-2">{t('fields.debit')}</th>
                            <th className="py-2">{t('fields.credit')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {ledger?.map((row, i) => (
                            <tr key={i} className="border-b">
                                <td className="py-2">{row.date_shamsi || row.date}</td>
                                <td className="py-2 capitalize">{row.type}</td>
                                <td className="py-2">{row.description}</td>
                                <td className="py-2">{row.debit ? formatCurrency(row.debit) : '-'}</td>
                                <td className="py-2">{row.credit ? formatCurrency(row.credit) : '-'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
