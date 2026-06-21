import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import { resourceData } from '@/utils/resource';

export default function Edit({ item }) {
    const { t } = useTranslation();
    const i = resourceData(item);
    const { data, setData, put, processing, errors } = useForm({
        name: i.name, sku: i.sku || '', unit: i.unit, category: i.category || '', min_stock: i.min_stock, current_stock: i.current_stock,
    });

    return (
        <ErpLayout>
            <Head title={t('inventory.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('inventory.edit')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); put(route('inventory.update', i.id)); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div><InputLabel value={t('fields.name')} /><TextInput className="mt-1 block w-full" value={data.name} onChange={(e) => setData('name', e.target.value)} /></div>
                <div><InputLabel value={t('fields.sku')} /><TextInput className="mt-1 block w-full" value={data.sku} onChange={(e) => setData('sku', e.target.value)} /></div>
                <div><InputLabel value={t('fields.unit')} /><TextInput className="mt-1 block w-full" value={data.unit} onChange={(e) => setData('unit', e.target.value)} /></div>
                <div><InputLabel value={t('fields.category')} /><TextInput className="mt-1 block w-full" value={data.category} onChange={(e) => setData('category', e.target.value)} /></div>
                <div className="grid gap-4 sm:grid-cols-2">
                    <div><InputLabel value={t('fields.min_stock')} /><TextInput type="number" step="0.001" className="mt-1 block w-full" value={data.min_stock} onChange={(e) => setData('min_stock', e.target.value)} /></div>
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
