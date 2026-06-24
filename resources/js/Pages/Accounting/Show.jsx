import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head } from '@inertiajs/react';

export default function Show({ entry }) {
    const { t } = useTranslation();
    const e = entry.data;
    const lines = e.lines?.data ?? e.lines ?? [];

    return (
        <ErpLayout>
            <Head title={t('accounting.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('accounting.show')} #{e.id}</h1>
                <ActionButtons editHref={!e.is_locked ? route('journal-entries.edit', e.id) : null} />
            </div>
            <div className="mb-6 rounded-lg bg-white p-4 shadow text-sm">
                <dl className="grid gap-2 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.entry_date')}</dt><dd>{e.entry_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.reference')}</dt><dd>{e.reference}</dd></div>
                    <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.description')}</dt><dd>{e.description}</dd></div>
                </dl>
            </div>
            <div className="rounded-lg bg-white p-4 shadow">
                <h2 className="mb-3 font-semibold">{t('accounting.lines')}</h2>
                <table className="min-w-full text-sm">
                    <thead><tr className="border-b text-left text-gray-500"><th className="py-2">{t('fields.account')}</th><th className="py-2">{t('fields.debit')}</th><th className="py-2">{t('fields.credit')}</th><th className="py-2">{t('fields.description')}</th></tr></thead>
                    <tbody>
                        {lines.map((line) => (
                            <tr key={line.id} className="border-b">
                                <td className="py-2">{line.account?.code} - {line.account?.name}</td>
                                <td className="py-2">{formatCurrency(line.debit)}</td>
                                <td className="py-2">{formatCurrency(line.credit)}</td>
                                <td className="py-2">{line.description}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
