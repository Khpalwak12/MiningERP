import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Create() {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        name: '', sku: '', unit: '', category: '', min_stock: '', current_stock: '0',
    });

    return (
        <ErpLayout>
            <Head title={t('inventory.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('inventory.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('inventory.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div><InputLabel value={t('fields.name')} required /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /><InputError message={errors.name} /></div>
                <div><InputLabel value={t('fields.sku')} /><TextInput className="mt-1 block w-full" value={data.sku} onChange={(e) => setData('sku', e.target.value)} /><InputError message={errors.sku} /></div>
                <div><InputLabel value={t('fields.unit')} required /><TextInput className="mt-1 block w-full" value={data.unit} onChange={(e) => setData('unit', e.target.value)} /><InputError message={errors.unit} /></div>
                <div><InputLabel value={t('fields.category')} /><TextInput className="mt-1 block w-full" value={data.category} onChange={(e) => setData('category', e.target.value)} /></div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div><InputLabel value={t('fields.min_stock')} required /><TextInput type="number" step="0.001" className="mt-1 block w-full" value={data.min_stock} onChange={(e) => setData('min_stock', e.target.value)} /><InputError message={errors.min_stock} /></div>
                    <div><InputLabel value={t('fields.current_stock')} /><TextInput type="number" step="0.001" className="mt-1 block w-full" value={data.current_stock} onChange={(e) => setData('current_stock', e.target.value)} /></div>
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('inventory.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
