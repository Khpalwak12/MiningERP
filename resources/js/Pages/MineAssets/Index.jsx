import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import useFinancialYearMode from '@/hooks/useFinancialYearMode';
import { MINE_ASSET_STATUSES } from '@/config/mineAssetConfig';
import { formatNumber } from '@/utils/format';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ assets, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const { canCreateTransactions, isTransactionReadOnly } = useFinancialYearMode();
    const [search, setSearch] = useState(filters?.search || '');
    const [status, setStatus] = useState(filters?.status || '');
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('mine-assets.index'), {
            search,
            status,
            date_from: dateFrom,
            date_to: dateTo,
        }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('mine_assets.title')} />
            <FlashMessage />
            <PageHeader
                title={t('mine_assets.title')}
                createRoute={canCreateTransactions && can('mine-assets.create') ? route('mine-assets.create') : null}
                createLabel={t('mine_assets.create')}
            />

            <form onSubmit={handleSearch} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <TextInput
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder={t('fields.search')}
                    className="max-w-xs"
                />
                <div>
                    <label className="block text-sm font-medium text-gray-700">{t('fields.status')}</label>
                    <select className="mt-1 rounded-md border-gray-300 text-sm" value={status} onChange={(e) => setStatus(e.target.value)}>
                        <option value="">{t('mine_assets.all_statuses')}</option>
                        {MINE_ASSET_STATUSES.map((s) => (
                            <option key={s} value={s}>{t(`mine_asset_statuses.${s}`)}</option>
                        ))}
                    </select>
                </div>
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.registration_date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.related_to')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.unit')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {assets?.data?.length === 0 && (
                            <tr><td colSpan={7} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {assets?.data?.map((asset) => (
                            <tr key={asset.id} className={asset.status === 'unusable' ? 'bg-gray-50' : ''}>
                                <td className="px-4 py-3">{asset.registration_date_shamsi}</td>
                                <td className="px-4 py-3 font-medium">{asset.name}</td>
                                <td className="px-4 py-3">{asset.related_to || '—'}</td>
                                <td className="px-4 py-3">{formatNumber(asset.quantity, 3)}</td>
                                <td className="px-4 py-3">{t(`mine_asset_units.${asset.unit}`, asset.unit)}</td>
                                <td className="px-4 py-3">
                                    <span className={`rounded px-2 py-0.5 text-xs ${asset.status === 'usable' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700'}`}>
                                        {t(`mine_asset_statuses.${asset.status}`)}
                                    </span>
                                </td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('mine-assets.show', asset.id)}
                                        editHref={!isTransactionReadOnly && can('mine-assets.edit') && !asset.is_locked ? route('mine-assets.edit', asset.id) : null}
                                        onDelete={!isTransactionReadOnly && can('mine-assets.delete') && !asset.is_locked ? () => router.delete(route('mine-assets.destroy', asset.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={assets} /></div>
            </div>
        </ErpLayout>
    );
}
