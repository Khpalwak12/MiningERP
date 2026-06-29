import ContractorRoyaltyNav from '@/Components/Erp/ContractorRoyaltyNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import useTodayShamsi from '@/hooks/useTodayShamsi';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Create() {
    const { t } = useTranslation();
    const today = useTodayShamsi();
    const { data, setData, post, processing, errors } = useForm({
        payment_date: today,
        amount: '',
        receipt_number: '',
        received_by: '',
        remarks: '',
    });

    return (
        <ErpLayout>
            <Head title={t('contractor_royalty.create_payment')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('contractor_royalty.create_payment')}</h1>
            <ContractorRoyaltyNav active="contractor-royalty.payments.index" />

            <form onSubmit={(e) => { e.preventDefault(); post(route('contractor-royalty.payments.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.payment_date} onChange={(v) => setData('payment_date', v)} error={errors.payment_date} required />
                <div>
                    <InputLabel value={t('fields.amount')} required />
                    <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} />
                    <InputError message={errors.amount} />
                </div>
                <div>
                    <InputLabel value={t('fields.receipt_number')} />
                    <TextInput className="mt-1 block w-full" value={data.receipt_number} onChange={(e) => setData('receipt_number', e.target.value)} />
                    <InputError message={errors.receipt_number} />
                </div>
                <div>
                    <InputLabel value={t('fields.received_by')} required />
                    <TextInput className="mt-1 block w-full" value={data.received_by} onChange={(e) => setData('received_by', e.target.value)} />
                    <InputError message={errors.received_by} />
                </div>
                <div>
                    <InputLabel value={t('fields.remarks')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.remarks} onChange={(e) => setData('remarks', e.target.value)} />
                    <InputError message={errors.remarks} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('contractor-royalty.payments.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
