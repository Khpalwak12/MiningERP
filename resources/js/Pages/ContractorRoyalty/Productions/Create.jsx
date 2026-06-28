import ContractorRoyaltyNav from '@/Components/Erp/ContractorRoyaltyNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { useMemo } from 'react';

export default function Create({ defaultRatePerTon }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        production_date: '',
        truck_number: '',
        quantity_ton: '',
        rate_per_ton: String(defaultRatePerTon ?? 150),
        remarks: '',
    });

    const totalRoyalty = useMemo(() => {
        const quantity = parseFloat(data.quantity_ton) || 0;
        const rate = parseFloat(data.rate_per_ton) || 0;

        return quantity * rate;
    }, [data.quantity_ton, data.rate_per_ton]);

    return (
        <ErpLayout>
            <Head title={t('contractor_royalty.create_production')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('contractor_royalty.create_production')}</h1>
            <ContractorRoyaltyNav active="contractor-royalty.productions.index" />

            <form onSubmit={(e) => { e.preventDefault(); post(route('contractor-royalty.productions.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.production_date} onChange={(v) => setData('production_date', v)} error={errors.production_date} required />
                <div>
                    <InputLabel value={t('fields.truck_number')} />
                    <TextInput className="mt-1 block w-full" value={data.truck_number} onChange={(e) => setData('truck_number', e.target.value)} />
                    <InputError message={errors.truck_number} />
                </div>
                <div>
                    <InputLabel value={t('fields.quantity_ton')} required />
                    <TextInput type="number" step="0.001" className="mt-1 block w-full" value={data.quantity_ton} onChange={(e) => setData('quantity_ton', e.target.value)} />
                    <InputError message={errors.quantity_ton} />
                </div>
                <div>
                    <InputLabel value={t('fields.rate_per_ton')} required />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.rate_per_ton} onChange={(e) => setData('rate_per_ton', e.target.value)} />
                    <InputError message={errors.rate_per_ton} />
                </div>
                <div>
                    <InputLabel value={t('contractor_royalty.total_royalty')} />
                    <div className="mt-1 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium">{formatCurrency(totalRoyalty)}</div>
                </div>
                <div>
                    <InputLabel value={t('fields.remarks')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.remarks} onChange={(e) => setData('remarks', e.target.value)} />
                    <InputError message={errors.remarks} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('contractor-royalty.productions.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
