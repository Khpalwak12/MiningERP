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

export default function Create() {
    const { t } = useTranslation();
    const today = useTodayShamsi();
    const { data, setData, post, processing, errors } = useForm({
        sale_date: today, truck_count: '', price_per_truck: '', discount: '', notes: '',
    });

    const subtotal = (parseInt(data.truck_count, 10) || 0) * (parseFloat(data.price_per_truck) || 0);
    const discount = parseFloat(data.discount) || 0;
    const total = Math.max(0, subtotal - discount);

    return (
        <ErpLayout>
            <Head title={t('sankari.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('sankari.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('sankari.store')); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <ShamsiDateInput label={t('fields.date')} value={data.sale_date} onChange={(v) => setData('sale_date', v)} error={errors.sale_date} required />
                <div className="grid gap-4 sm:grid-cols-2">
                    <div><InputLabel value={t('fields.truck_count')} required /><TextInput type="number" min="1" className="mt-1 block w-full" value={data.truck_count} onChange={(e) => setData('truck_count', e.target.value)} /><InputError message={errors.truck_count} /></div>
                    <div><InputLabel value={t('fields.price_per_truck')} required /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.price_per_truck} onChange={(e) => setData('price_per_truck', e.target.value)} /><InputError message={errors.price_per_truck} /></div>
                </div>
                <div><InputLabel value={t('fields.discount')} /><TextInput type="number" step="0.01" min="0" className="mt-1 block w-full" value={data.discount} onChange={(e) => setData('discount', e.target.value)} /><InputError message={errors.discount} /></div>
                <div className="space-y-1 rounded-md bg-indigo-50 p-3 text-sm font-medium text-indigo-800">
                    <div>{t('fields.subtotal')}: {formatCurrency(subtotal)}</div>
                    <div>{t('fields.total_amount')}: {formatCurrency(total)}</div>
                </div>
                <div><InputLabel value={t('fields.notes')} /><textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.notes} onChange={(e) => setData('notes', e.target.value)} /></div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('sankari.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
