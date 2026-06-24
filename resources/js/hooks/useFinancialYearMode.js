import { usePage } from '@inertiajs/react';

export default function useFinancialYearMode() {
    const { isAllYearsMode, activeFinancialYear } = usePage().props;

    const canCreateTransactions = !isAllYearsMode;

    return {
        isAllYearsMode: Boolean(isAllYearsMode),
        activeFinancialYear,
        canCreateTransactions,
        isTransactionReadOnly: Boolean(isAllYearsMode),
    };
}
