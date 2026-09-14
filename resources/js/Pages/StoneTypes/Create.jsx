import ShipmentsNav from '@/Components/Erp/ShipmentsNav';
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
        name_en: '',
        name_ps: '',
        description: '',
    });

    return (
        <ErpLayout>
            <Head title={t('stone_types.create')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('stone_types.create')}</h1>
            <ShipmentsNav active="stone-types.index" />

            <form onSubmit={(e) => { e.preventDefault(); post(route('stone-types.store')); }} className="max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow">
                <div>
                    <InputLabel value={t('stone_types.name_en')} required />
                    <TextInput className="mt-1 block w-full" value={data.name_en} onChange={(e) => setData('name_en', e.target.value)} />
                    <InputError message={errors.name_en} />
                </div>
                <div>
                    <InputLabel value={t('stone_types.name_ps')} required />
                    <TextInput className="mt-1 block w-full" value={data.name_ps} onChange={(e) => setData('name_ps', e.target.value)} dir="rtl" />
                    <InputError message={errors.name_ps} />
                </div>
                <div>
                    <InputLabel value={t('fields.description')} />
                    <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="3" value={data.description} onChange={(e) => setData('description', e.target.value)} />
                    <InputError message={errors.description} />
                </div>
                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('stone-types.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
