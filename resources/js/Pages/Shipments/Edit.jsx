import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import CustomerSelect from '@/Components/Erp/CustomerSelect';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { shipmentStatusBadgeClass } from '@/utils/shipmentStatus';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

function calcShipmentTotal(quantityTon, pricePerTon) {
    const qty = parseFloat(quantityTon);
    const price = parseFloat(pricePerTon);
    const hasQty = quantityTon !== '' && quantityTon !== null && !Number.isNaN(qty);
    const hasPrice = pricePerTon !== '' && pricePerTon !== null && !Number.isNaN(price);

    if (hasQty && hasPrice) {
        return qty * price;
    }

    return null;
}

export default function Edit({ shipment, customers }) {
    const { t } = useTranslation();
    const s = resourceData(shipment);
    const { data, setData, put, processing, errors } = useForm({
        shipment_date: s.shipment_date_shamsi || '', customer_id: s.customer_id || '', driver_name: s.driver_name || '',
        quantity_ton: s.quantity_ton ?? '', price_per_ton: s.price_per_ton ?? '', notes: s.notes || '',
    });
    const total = calcShipmentTotal(data.quantity_ton, data.price_per_ton);

    return (
        <ErpLayout>
            <Head title={t('shipments.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('shipments.edit')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); put(route('shipments.update', s.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                {s.status && (
                    <span className={`inline-flex rounded-full px-3 py-1 text-xs font-medium ${shipmentStatusBadgeClass(s.status)}`}>
                        {t(`shipments.statuses.${s.status}`)}
                    </span>
                )}
                <p className="text-sm text-gray-600">{t('shipments.optional_weight_price')}</p>
                <ShamsiDateInput label={t('fields.date')} value={data.shipment_date} onChange={(v) => setData('shipment_date', v)} error={errors.shipment_date} required />
                <div>
                    <InputLabel value={t('fields.customer')} required />
                    <CustomerSelect
                        className="mt-1"
                        customers={customers}
                        value={data.customer_id}
                        onChange={(customerId) => setData('customer_id', customerId)}
                        error={errors.customer_id}
                        selectedCustomer={resourceData(s.customer)}
                    />
                    <InputError message={errors.customer_id} />
                </div>
                <div>
                    <InputLabel value={t('fields.driver_name')} />
                    <TextInput className="mt-1 block w-full" value={data.driver_name} onChange={(e) => setData('driver_name', e.target.value)} />
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div><InputLabel value={t('fields.quantity_ton')} /><TextInput type="number" step="0.001" className="mt-1 block w-full" value={data.quantity_ton} onChange={(e) => setData('quantity_ton', e.target.value)} placeholder="—" /><InputError message={errors.quantity_ton} /></div>
                    <div><InputLabel value={t('fields.price_per_ton')} /><TextInput type="number" step="0.01" className="mt-1 block w-full" value={data.price_per_ton} onChange={(e) => setData('price_per_ton', e.target.value)} placeholder="—" /><InputError message={errors.price_per_ton} /></div>
                </div>
                <div className={`rounded-md p-3 text-sm font-medium ${total !== null ? 'bg-indigo-50 text-indigo-800' : 'bg-amber-50 text-amber-800'}`}>
                    {t('fields.total_amount')}: {total !== null ? formatCurrency(total) : t('shipments.pending_total')}
                </div>
                <div><InputLabel value={t('fields.notes')} /><textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.notes} onChange={(e) => setData('notes', e.target.value)} /></div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('shipments.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
