import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceData } from '@/utils/resource';

export default function Create({ item }) {
    const { t } = useTranslation();
    const i = resourceData(item);
    const { data, setData, post, processing, errors } = useForm({
        inventory_item_id: i.id, movement_date: '', movement_type: 'in', quantity: '', reference: '', notes: '',
    });

    return (
        <ErpLayout>
            <Head title={t('inventory.add_movement')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('inventory.add_movement')} - {i.name}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('inventory.movements.store', i.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.date')} value={data.movement_date} onChange={(v) => setData('movement_date', v)} error={errors.movement_date} required />
                <div>
                    <InputLabel value={t('fields.movement_type')} />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.movement_type} onChange={(e) => setData('movement_type', e.target.value)}>
                        <option value="in">{t('movement_types.in')}</option>
                        <option value="out">{t('movement_types.out')}</option>
                    </select>
                </div>
                <div><InputLabel value={t('fields.quantity')} required /><TextInput type="number" step="0.001" className="mt-1 block w-full" value={data.quantity} onChange={(e) => setData('quantity', e.target.value)} /><InputError message={errors.quantity} /></div>
                <div><InputLabel value={t('fields.reference')} /><TextInput className="mt-1 block w-full" value={data.reference} onChange={(e) => setData('reference', e.target.value)} /></div>
                <div><InputLabel value={t('fields.notes')} /><textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.notes} onChange={(e) => setData('notes', e.target.value)} /></div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('inventory.movements.index', i.id)}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
