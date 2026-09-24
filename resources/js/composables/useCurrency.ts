import { computed, ref } from 'vue';

export type Currency = 'USD' | 'EUR' | 'UAH';
const rates: Record<Currency, number> = { USD: 1, EUR: 0.92, UAH: 41.25 };
const symbols: Record<Currency, string> = { USD: '$', EUR: '€', UAH: '₴' };
const initial = typeof window !== 'undefined' && ['USD', 'EUR', 'UAH'].includes(localStorage.getItem('nova_currency') ?? '') ? localStorage.getItem('nova_currency') as Currency : 'USD';
const currency = ref<Currency>(initial);

export function useCurrency() {
    const setCurrency = (next: Currency) => {
        currency.value = next;
        if (typeof window !== 'undefined') localStorage.setItem('nova_currency', next);
    };
    const formatPrice = (value: string | number) => {
        const amount = Number(value) * rates[currency.value];
        return `${symbols[currency.value]}${new Intl.NumberFormat(currency.value === 'UAH' ? 'uk-UA' : 'en-US', { maximumFractionDigits: 0 }).format(amount)}`;
    };
    return { currency: computed(() => currency.value), setCurrency, formatPrice };
}
