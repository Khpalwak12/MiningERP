import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import useTodayShamsi from '@/hooks/useTodayShamsi';
import { PERSONAL_CURRENCIES } from '@/config/personalAccountConfig';
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
        purchase_date: today, item_name: '', bill_number: '', currency: 'AFN', amount: '', description: '',
    });

    return (
        <ErpLayout>
            <Head title={t('machinery.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('machinery.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('machinery.store')); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <ShamsiDateInput label={t('fields.date')} value={data.purchase_date} onChange={(v) => setData('purchase_date', v)} error={errors.purchase_date} required />
                <div>
                    <InputLabel value={t('fields.name')} required />
                    <TextInput className="mt-1 block w-full" value={data.item_name} onChange={(e) => setData('item_name', e.target.value)} />
                    <InputError message={errors.item_name} />
                </div>
                <div>
                    <InputLabel value={t('fields.bill_number')} />
                    <TextInput className="mt-1 block w-full" value={data.bill_number} onChange={(e) => setData('bill_number', e.target.value)} placeholder="INV-001" />
                    <InputError message={errors.bill_number} />
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel value={t('fields.currency')} required />
                        <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.currency} onChange={(e) => setData('currency', e.target.value)}>
                            {PERSONAL_CURRENCIES.map((c) => <option key={c} value={c}>{t(`currencies.${c}`)}</option>)}
                        </select>
                        <InputError message={errors.currency} />
                    </div>
                    <div>
                        <InputLabel value={t('fields.amount')} required />
                        <TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.amount} onChange={(e) => setData('amount', e.target.value)} />
                        <InputError message={errors.amount} />
                    </div>
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('machinery.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
