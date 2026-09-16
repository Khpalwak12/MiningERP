import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceData, resourceItems } from '@/utils/resource';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import DangerButton from '@/Components/DangerButton';

export default function Edit({ entry, accounts }) {
    const { t } = useTranslation();
    const entryData = resourceData(entry);
    const existingLines = resourceItems(entryData.lines).map((l) => ({
        account_id: l.account_id, debit: l.debit, credit: l.credit, description: l.description || '',
    }));

    const { data, setData, put, processing, errors } = useForm({
        entry_date: entryData.entry_date_shamsi || entryData.entry_date || '',
        reference: entryData.reference || '',
        description: entryData.description || '',
        lines: existingLines.length ? existingLines : [{ account_id: '', debit: '', credit: '', description: '' }, { account_id: '', debit: '', credit: '', description: '' }],
    });

    const accountList = resourceItems(accounts);

    const updateLine = (index, field, value) => {
        const lines = [...data.lines];
        lines[index] = { ...lines[index], [field]: value };
        setData('lines', lines);
    };

    const addLine = () => setData('lines', [...data.lines, { account_id: '', debit: '', credit: '', description: '' }]);
    const removeLine = (index) => setData('lines', data.lines.filter((_, i) => i !== index));

    return (
        <ErpLayout>
            <Head title={t('accounting.edit')} />
            <FlashMessage />
            <h1 className="mb-6 text-center text-2xl font-bold text-slate-800">{t('accounting.edit')}</h1>

            <form onSubmit={(ev) => { ev.preventDefault(); put(route('journal-entries.update', entryData.id)); }} className="mx-auto w-full max-w-4xl space-y-5 rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50/80 p-6 shadow-md ring-1 ring-slate-900/5 sm:p-8">
                <ShamsiDateInput label={t('fields.entry_date')} value={data.entry_date} onChange={(v) => setData('entry_date', v)} error={errors.entry_date} required />
                <div><InputLabel value={t('fields.reference')} /><TextInput className="mt-1 block w-full" value={data.reference} onChange={(ev) => setData('reference', ev.target.value)} /></div>
                <div><InputLabel value={t('fields.description')} /><textarea className="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" value={data.description} onChange={(ev) => setData('description', ev.target.value)} /></div>
                <div className="space-y-3">
                    <InputError message={errors.lines} />
                    {data.lines.map((line, index) => (
                        <div key={index} className="grid gap-2 rounded border p-3 sm:grid-cols-5">
                            <select className="rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:col-span-2" value={line.account_id} onChange={(ev) => updateLine(index, 'account_id', ev.target.value)}>
                                <option value="">--</option>
                                {accountList.map((a) => <option key={a.id} value={a.id}>{a.code} - {a.name}</option>)}
                            </select>
                            <TextInput type="number" step="0.01" placeholder={t('fields.debit')} value={line.debit} onChange={(ev) => updateLine(index, 'debit', ev.target.value)} />
                            <TextInput type="number" step="0.01" placeholder={t('fields.credit')} value={line.credit} onChange={(ev) => updateLine(index, 'credit', ev.target.value)} />
                            <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                                <TextInput value={line.description} onChange={(ev) => updateLine(index, 'description', ev.target.value)} />
                                {data.lines.length > 2 && <DangerButton type="button" onClick={() => removeLine(index)}>×</DangerButton>}
                            </div>
                        </div>
                    ))}
                    <SecondaryButton type="button" onClick={addLine}>{t('actions.create')}</SecondaryButton>
                </div>
                <div className="flex flex-wrap items-center justify-center gap-3 border-t border-slate-200/80 pt-5">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('journal-entries.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
