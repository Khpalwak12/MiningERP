import ContractorRoyaltyNav from '@/Components/Erp/ContractorRoyaltyNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import useTodayShamsi from '@/hooks/useTodayShamsi';
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
    const today = useTodayShamsi();
    const { data, setData, post, processing, errors } = useForm({
        production_date: today,
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
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('contractor_royalty.create_production')}</h1>
            <ContractorRoyaltyNav active="contractor-royalty.productions.index" />

            <form onSubmit={(e) => { e.preventDefault(); post(route('contractor-royalty.productions.store')); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <ShamsiDateInput label={t('fields.date')} value={data.production_date} onChange={(v) => setData('production_date', v)} error={errors.production_date} required />
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
                    <textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.remarks} onChange={(e) => setData('remarks', e.target.value)} />
                    <InputError message={errors.remarks} />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('contractor-royalty.productions.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
