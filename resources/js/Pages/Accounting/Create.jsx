import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import useTodayShamsi from '@/hooks/useTodayShamsi';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link, useForm } from '@inertiajs/react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import DangerButton from '@/Components/DangerButton';

const emptyLine = () => ({ account_id: '', debit: '', credit: '', description: '' });

export default function Create({ accounts }) {
    const { t } = useTranslation();
    const today = useTodayShamsi();
    const { data, setData, post, processing, errors } = useForm({
        entry_date: today, reference: '', description: '', lines: [emptyLine(), emptyLine()],
    });

    const accountList = resourceItems(accounts);

    const updateLine = (index, field, value) => {
        const lines = [...data.lines];
        lines[index] = { ...lines[index], [field]: value };
        setData('lines', lines);
    };

    const addLine = () => setData('lines', [...data.lines, emptyLine()]);
    const removeLine = (index) => setData('lines', data.lines.filter((_, i) => i !== index));

    const totalDebit = data.lines.reduce((s, l) => s + (parseFloat(l.debit) || 0), 0);
    const totalCredit = data.lines.reduce((s, l) => s + (parseFloat(l.credit) || 0), 0);

    return (
        <ErpLayout>
            <Head title={t('accounting.create')} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{t('accounting.create')}</h1>

            <form onSubmit={(e) => { e.preventDefault(); post(route('journal-entries.store')); }} className="max-w-4xl space-y-4 rounded-lg bg-white p-6 shadow">
                <ShamsiDateInput label={t('fields.entry_date')} value={data.entry_date} onChange={(v) => setData('entry_date', v)} error={errors.entry_date} required />
                <div><InputLabel value={t('fields.reference')} /><TextInput className="mt-1 block w-full" value={data.reference} onChange={(e) => setData('reference', e.target.value)} /></div>
                <div><InputLabel value={t('fields.description')} /><textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.description} onChange={(e) => setData('description', e.target.value)} /></div>

                <div>
                    <div className="mb-2 flex items-center justify-between">
                        <InputLabel value={t('accounting.lines')} />
                        <SecondaryButton type="button" onClick={addLine}>{t('actions.create')}</SecondaryButton>
                    </div>
                    <InputError message={errors.lines} />
                    <div className="space-y-3">
                        {data.lines.map((line, index) => (
                            <div key={index} className="grid gap-2 rounded border p-3 sm:grid-cols-5">
                                <select className="rounded-md border-gray-300 sm:col-span-2" value={line.account_id} onChange={(e) => updateLine(index, 'account_id', e.target.value)}>
                                    <option value="">-- {t('fields.account')} --</option>
                                    {accountList.map((a) => <option key={a.id} value={a.id}>{a.code} - {a.name}</option>)}
                                </select>
                                <TextInput type="number" step="0.01" placeholder={t('fields.debit')} value={line.debit} onChange={(e) => updateLine(index, 'debit', e.target.value)} />
                                <TextInput type="number" step="0.01" placeholder={t('fields.credit')} value={line.credit} onChange={(e) => updateLine(index, 'credit', e.target.value)} />
                                <div className="flex gap-2">
                                    <TextInput placeholder={t('fields.description')} value={line.description} onChange={(e) => updateLine(index, 'description', e.target.value)} />
                                    {data.lines.length > 2 && <DangerButton type="button" onClick={() => removeLine(index)}>×</DangerButton>}
                                </div>
                            </div>
                        ))}
                    </div>
                    <p className="mt-2 text-sm text-gray-600">{t('fields.debit')}: {formatCurrency(totalDebit)} | {t('fields.credit')}: {formatCurrency(totalCredit)}</p>
                </div>

                <div className="flex gap-2">
                    <PrimaryButton disabled={processing}>{t('actions.save')}</PrimaryButton>
                    <Link href={route('journal-entries.index')}><SecondaryButton type="button">{t('actions.cancel')}</SecondaryButton></Link>
                </div>
            </form>
        </ErpLayout>
    );
}
