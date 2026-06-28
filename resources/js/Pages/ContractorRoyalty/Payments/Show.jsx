import ContractorRoyaltyNav from '@/Components/Erp/ContractorRoyaltyNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Show({ payment }) {
    const { t } = useTranslation();
    const p = resourceData(payment);

    return (
        <ErpLayout>
            <Head title={t('contractor_royalty.payment_details')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('contractor_royalty.payment_details')}</h1>
                <Link href={route('contractor-royalty.payments.index')}><SecondaryButton>{t('actions.back')}</SecondaryButton></Link>
            </div>
            <ContractorRoyaltyNav active="contractor-royalty.payments.index" />

            <div className="max-w-2xl rounded-lg bg-white p-6 shadow">
                <dl className="grid gap-4 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.date')}</dt><dd>{p.payment_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.amount')}</dt><dd className="font-semibold">{formatCurrency(p.amount)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.receipt_number')}</dt><dd>{p.receipt_number || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.received_by')}</dt><dd>{p.received_by}</dd></div>
                    <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.remarks')}</dt><dd>{p.remarks || '—'}</dd></div>
                </dl>
            </div>
        </ErpLayout>
    );
}
