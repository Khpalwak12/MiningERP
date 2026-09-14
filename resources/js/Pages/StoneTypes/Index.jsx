import ActionButtons from '@/Components/Erp/ActionButtons';
import ShipmentsNav from '@/Components/Erp/ShipmentsNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { resourceItems } from '@/utils/resource';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ stoneTypes, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [search, setSearch] = useState(filters?.search || '');
    const list = resourceItems(stoneTypes);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('stone-types.index'), { search }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('stone_types.title')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('shipments.title')}</h1>
            <ShipmentsNav active="stone-types.index" />
            <PageHeader
                title={t('stone_types.title')}
                createRoute={can('stone-types.create') ? route('stone-types.create') : null}
                createLabel={t('stone_types.create')}
            />

            <form onSubmit={handleSearch} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <TextInput
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder={t('fields.search')}
                    className="max-w-xs"
                />
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('stone_types.name_en')}</th>
                            <th className="px-4 py-3 text-left">{t('stone_types.name_ps')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((stoneType) => (
                            <tr key={stoneType.id}>
                                <td className="px-4 py-3 font-medium">{stoneType.name_en}</td>
                                <td className="px-4 py-3">{stoneType.name_ps}</td>
                                <td className="px-4 py-3">{stoneType.description || '—'}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={can('stone-types.edit') ? route('stone-types.edit', stoneType.id) : null}
                                        onDelete={can('stone-types.delete') ? () => router.delete(route('stone-types.destroy', stoneType.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={stoneTypes} /></div>
            </div>
        </ErpLayout>
    );
}
