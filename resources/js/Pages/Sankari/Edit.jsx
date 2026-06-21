import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceData } from '@/utils/resource';

export default function Edit({ sale }) {
    const { t } = useTranslation();
    const s = resourceData(sale);
    const { data, setData, put, processing, errors } = useForm({
        sale_date: s.sale_date_shamsi || s.sale_date || '',
        truck_count: s.truck_count,
        price_per_truck: s.price_per_truck,
        payment_type: s.payment_type,
        cash_received: s.cash_received,
        notes: s.notes || '',
    });

    const total = (parseInt(data.truck_count, 10) || 0) * (parseFloat(data.price_per_truck) || 0);

    return (
        <ErpLayout>
            <Head title={t('sankari.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('sankari.edit')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); put(route('sankari.update', s.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.sale_date} onChange={(v) => setData('sale_date', v)} error={errors.sale_date} required />
                <div className="grid gap-4 sm:grid-cols-2">
                    <div><InputLabel value={t('fields.truck_count')} /><TextInput type="number" className="mt-1 block w-full" value={data.truck_count} onChange={(e) => setData('truck_count', e.target.value)} /></div>
                    <div><InputLabel value={t('fields.price_per_truck')} /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.price_per_truck} onChange={(e) => setData('price_per_truck', e.target.value)} /></div>
                </div>
                <div className="rounded-md bg-indigo-50 p-3 text-sm font-medium text-indigo-800">{t('fields.total_amount')}: {formatCurrency(total)}</div>
                <div>
                    <InputLabel value={t('fields.payment_type')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.payment_type} onChange={(e) => setData('payment_type', e.target.value)}>
                        <option value="cash">{t('payment_types.cash')}</option>
                        <option value="credit">{t('payment_types.credit')}</option>
                    </select>
                </div>
                <div><InputLabel value={t('fields.cash_received')} /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.cash_received} onChange={(e) => setData('cash_received', e.target.value)} /></div>
                <div><InputLabel value={t('fields.notes')} /><textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.notes} onChange={(e) => setData('notes', e.target.value)} /></div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('sankari.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
