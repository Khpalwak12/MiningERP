import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import useTodayShamsi from '@/hooks/useTodayShamsi';
import { MINE_ASSET_STATUSES, MINE_ASSET_UNITS } from '@/config/mineAssetConfig';
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
        name: '',
        related_to: '',
        quantity: '1',
        unit: 'piece',
        status: 'usable',
        registration_date: today,
        remarks: '',
    });

    return (
        <ErpLayout>
            <Head title={t('mine_assets.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('mine_assets.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('mine-assets.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.registration_date')} value={data.registration_date} onChange={(v) => setData('registration_date', v)} error={errors.registration_date} required />
                <div>
                    <InputLabel value={t('fields.name')} required />
                    <TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} placeholder={t('mine_assets.title')} />
                    <InputError message={errors.name} />
                </div>
                <div>
                    <InputLabel value={t('fields.related_to')} />
                    <TextInput className="mt-1 block w-full" value={data.related_to} onChange={(e) => setData('related_to', e.target.value)} />
                    <InputError message={errors.related_to} />
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel value={t('fields.quantity')} required />
                        <TextInput type="number" step="0.001" min="0.001" className="mt-1 block w-full" value={data.quantity} onChange={(e) => setData('quantity', e.target.value)} />
                        <InputError message={errors.quantity} />
                    </div>
                    <div>
                        <InputLabel value={t('fields.unit')} required />
                        <select className="mt-1 block w-full rounded-md border-gray-300" value={data.unit} onChange={(e) => setData('unit', e.target.value)}>
                            {MINE_ASSET_UNITS.map((unit) => (
                                <option key={unit} value={unit}>{t(`mine_asset_units.${unit}`)}</option>
                            ))}
                        </select>
                        <InputError message={errors.unit} />
                    </div>
                </div>
                <div>
                    <InputLabel value={t('fields.status')} required />
                    <select className="mt-1 block w-full rounded-md border-gray-300" value={data.status} onChange={(e) => setData('status', e.target.value)}>
                        {MINE_ASSET_STATUSES.map((s) => (
                            <option key={s} value={s}>{t(`mine_asset_statuses.${s}`)}</option>
                        ))}
                    </select>
                    <InputError message={errors.status} />
                </div>
                <div>
                    <InputLabel value={t('fields.notes')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.remarks} onChange={(e) => setData('remarks', e.target.value)} />
                    <InputError message={errors.remarks} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('mine-assets.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
