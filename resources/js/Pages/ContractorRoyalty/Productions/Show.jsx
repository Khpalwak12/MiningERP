import ContractorRoyaltyNav from '@/Components/Erp/ContractorRoyaltyNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency, formatNumber } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Show({ production }) {
    const { t } = useTranslation();
    const p = resourceData(production);

    return (
        <ErpLayout>
            <Head title={t('contractor_royalty.production_details')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('contractor_royalty.production_details')}</h1>
                <Link href={route('contractor-royalty.productions.index')}><SecondaryButton>{t('actions.back')}</SecondaryButton></Link>
            </div>
            <ContractorRoyaltyNav active="contractor-royalty.productions.index" />

            <div className="max-w-2xl rounded-lg bg-white p-6 shadow">
                <dl className="grid gap-4 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.date')}</dt><dd>{p.production_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.truck_number')}</dt><dd>{p.truck_number || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.quantity_ton')}</dt><dd>{formatNumber(p.quantity_ton, 3)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.rate_per_ton')}</dt><dd>{formatCurrency(p.rate_per_ton)}</dd></div>
                    <div><dt className="text-gray-500">{t('contractor_royalty.total_royalty')}</dt><dd className="font-semibold">{formatCurrency(p.total_royalty)}</dd></div>
                    <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.remarks')}</dt><dd>{p.remarks || '—'}</dd></div>
                </dl>
            </div>
        </ErpLayout>
    );
}
