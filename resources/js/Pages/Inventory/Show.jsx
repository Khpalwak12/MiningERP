import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Show({ item }) {
    const { t } = useTranslation();
    const i = item.data;

    return (
        <ErpLayout>
            <Head title={t('inventory.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{i.name}</h1>
                <div className="flex gap-2">
                    <Link href={route('inventory.movements.index', i.id)}><PrimaryButton>{t('inventory.movements')}</PrimaryButton></Link>
                    <ActionButtons editHref={route('inventory.edit', i.id)} />
                </div>
            </div>
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <div className="rounded-lg bg-white p-4 shadow"><p className="text-sm text-gray-500">{t('fields.current_stock')}</p><p className="text-xl font-bold">{i.current_stock} {i.unit}</p></div>
                <div className="rounded-lg bg-white p-4 shadow"><p className="text-sm text-gray-500">{t('fields.min_stock')}</p><p className="text-xl font-bold">{i.min_stock}</p></div>
                <div className="rounded-lg bg-white p-4 shadow"><p className="text-sm text-gray-500">{t('fields.sku')}</p><p className="text-xl font-bold">{i.sku || '-'}</p></div>
            </div>
            {i.movements?.data?.length > 0 && (
                <div className="rounded-lg bg-white p-4 shadow">
                    <h2 className="mb-3 font-semibold">{t('inventory.movements')}</h2>
                    <table className="min-w-full text-sm">
                        <thead><tr className="border-b text-left text-gray-500"><th className="py-2">{t('fields.date')}</th><th className="py-2">{t('fields.movement_type')}</th><th className="py-2">{t('fields.quantity')}</th></tr></thead>
                        <tbody>
                            {i.movements.data.map((m) => (
                                <tr key={m.id} className="border-b"><td className="py-2">{m.movement_date_shamsi}</td><td className="py-2">{t(`movement_types.${m.movement_type}`)}</td><td className="py-2">{m.quantity}</td></tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </ErpLayout>
    );
}
