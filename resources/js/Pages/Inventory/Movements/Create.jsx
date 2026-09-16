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
import { resourceData } from '@/utils/resource';

export default function Create({ item }) {
    const { t } = useTranslation();
    const today = useTodayShamsi();
    const i = resourceData(item);
    const { data, setData, post, processing, errors } = useForm({
        inventory_item_id: i.id, movement_date: today, movement_type: 'in', quantity: '', reference: '', notes: '',
    });

    return (
        <ErpLayout>
            <Head title={t('inventory.add_movement')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('inventory.add_movement')} - {i.name}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('inventory.movements.store', i.id)); }} className="mx-auto w-full max-w-2xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <ShamsiDateInput label={t('fields.date')} value={data.movement_date} onChange={(v) => setData('movement_date', v)} error={errors.movement_date} required />
                <div>
                    <InputLabel value={t('fields.movement_type')} />
                    <select className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value={data.movement_type} onChange={(e) => setData('movement_type', e.target.value)}>
                        <option value="in">{t('movement_types.in')}</option>
                        <option value="out">{t('movement_types.out')}</option>
                    </select>
                </div>
                <div><InputLabel value={t('fields.quantity')} required /><TextInput type="number" step="0.001" className="mt-1 block w-full" value={data.quantity} onChange={(e) => setData('quantity', e.target.value)} /><InputError message={errors.quantity} /></div>
                <div><InputLabel value={t('fields.reference')} /><TextInput className="mt-1 block w-full" value={data.reference} onChange={(e) => setData('reference', e.target.value)} /></div>
                <div><InputLabel value={t('fields.notes')} /><textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.notes} onChange={(e) => setData('notes', e.target.value)} /></div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('inventory.movements.index', i.id)}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
