import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatNumber } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function MineAssets({ rows, filters, total }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.mine_assets')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.mine_assets')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="mine-assets"
                routeName="reports.mine-assets"
                filters={filters}
                exportType="mine-assets"
                showMineAssetStatus
            />

            <p className="mb-4 text-sm text-gray-600">
                {t('reports.total_records')}: {list.length}
                {total != null && ` · ${t('fields.quantity')}: ${formatNumber(total, 3)}`}
            </p>

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.registration_date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.related_to')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.unit')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.notes')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={7} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((r) => (
                            <tr key={r.id} className={r.status === 'unusable' ? 'bg-gray-50' : ''}>
                                <td className="px-4 py-3">{r.registration_date_shamsi}</td>
                                <td className="px-4 py-3 font-medium">{r.name}</td>
                                <td className="px-4 py-3">{r.related_to || '—'}</td>
                                <td className="px-4 py-3">{formatNumber(r.quantity, 3)}</td>
                                <td className="px-4 py-3">{t(`mine_asset_units.${r.unit}`, r.unit)}</td>
                                <td className="px-4 py-3">{t(`mine_asset_statuses.${r.status}`)}</td>
                                <td className="px-4 py-3">{r.remarks || '—'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
