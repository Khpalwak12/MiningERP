import CustomerSelect from '@/Components/Erp/CustomerSelect';
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

export default function Create({ customers }) {
    const { t } = useTranslation();
    const today = useTodayShamsi();
    const { data, setData, post, processing, errors } = useForm({
        customer_id: '',
        payment_date: today,
        amount: '',
        receipt_number: '',
        received_by: '',
        notes: '',
    });

    return (
        <ErpLayout>
            <Head title={t('payments.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('payments.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('payments.store')); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <div>
                    <InputLabel value={t('fields.customer')} required />
                    <CustomerSelect
                        className="mt-1"
                        customers={customers}
                        value={data.customer_id}
                        onChange={(customerId) => setData('customer_id', customerId)}
                        error={errors.customer_id}
                    />
                    <InputError message={errors.customer_id} />
                </div>
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
                    <InputLabel value={t('fields.notes')} />
                    <textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.notes} onChange={(e) => setData('notes', e.target.value)} />
                    <InputError message={errors.notes} />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('payments.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
