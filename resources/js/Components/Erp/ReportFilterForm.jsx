import AdvancedReportFilter from '@/Components/Erp/AdvancedReportFilter';
import CustomerSelect from '@/Components/Erp/CustomerSelect';
import ReportExportButtons from '@/Components/Erp/ReportExportButtons';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import PrimaryButton from '@/Components/PrimaryButton';
import useTranslation from '@/hooks/useTranslation';
import { SHIPMENT_STATUS_OPTIONS } from '@/config/reportFilterConfig';
import { MINE_ASSET_STATUSES } from '@/config/mineAssetConfig';
import { buildExportQuery } from '@/utils/reportExport';
import { resourceData } from '@/utils/resource';
import { router } from '@inertiajs/react';
import { useState } from 'react';

export default function ReportFilterForm({
    reportType,
    routeName,
    filters = {},
    exportType,
    lookups = {},
    showCustomer = false,
    showStatus = false,
    showMineAssetStatus = false,
    children,
}) {
    const { t } = useTranslation();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');
    const [customerId, setCustomerId] = useState(filters?.customer_id || '');
    const [status, setStatus] = useState(filters?.status || '');
    const [filterBy, setFilterBy] = useState(filters?.filter_by || '');
    const [filterValue, setFilterValue] = useState(filters?.filter_value ?? '');

    const buildParams = () => {
        const params = {
            date_from: dateFrom,
            date_to: dateTo,
        };

        if (showCustomer) {
            params.customer_id = customerId;
        }

        if (showStatus || showMineAssetStatus) {
            params.status = status;
        }

        if (filterBy) {
            params.filter_by = filterBy;
            if (filterValue !== '' && filterValue != null) {
                params.filter_value = filterValue;
            }
        }

        return params;
    };

    const handleFilter = (e) => {
        e.preventDefault();
        router.get(route(routeName), buildParams(), { preserveState: true });
    };

    const handleFilterByChange = (value) => {
        setFilterBy(value);
        setFilterValue('');
    };

    const exportQuery = buildExportQuery(buildParams());

    return (
        <form onSubmit={handleFilter} className="mb-4 rounded-lg bg-white p-4 shadow">
            <div className="flex flex-wrap items-end gap-4">
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />

                {showCustomer && (
                    <div className="min-w-[220px]">
                        <label className="mb-1 block text-sm font-medium text-gray-700">{t('fields.customer')}</label>
                        <CustomerSelect
                            customers={lookups.customers}
                            value={customerId}
                            onChange={setCustomerId}
                            selectedCustomer={resourceData(lookups.selectedCustomer)}
                        />
                    </div>
                )}

                {showStatus && (
                    <div>
                        <label className="block text-sm font-medium text-gray-700">{t('fields.status')}</label>
                        <select
                            className="mt-1 rounded-md border-gray-300 text-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">{t('actions.filter')} — {t('fields.status')}</option>
                            {SHIPMENT_STATUS_OPTIONS.map((option) => (
                                <option key={option} value={option}>{t(`shipments.statuses.${option}`)}</option>
                            ))}
                        </select>
                    </div>
                )}

                {showMineAssetStatus && (
                    <div>
                        <label className="block text-sm font-medium text-gray-700">{t('fields.status')}</label>
                        <select
                            className="mt-1 rounded-md border-gray-300 text-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">{t('mine_assets.all_statuses')}</option>
                            {MINE_ASSET_STATUSES.map((option) => (
                                <option key={option} value={option}>{t(`mine_asset_statuses.${option}`)}</option>
                            ))}
                        </select>
                    </div>
                )}

                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
                {exportType && <ReportExportButtons exportType={exportType} queryString={exportQuery} />}
                {children}
            </div>

            <AdvancedReportFilter
                reportType={reportType}
                filterBy={filterBy}
                filterValue={filterValue}
                onFilterByChange={handleFilterByChange}
                onFilterValueChange={setFilterValue}
                lookups={lookups}
            />
        </form>
    );
}
