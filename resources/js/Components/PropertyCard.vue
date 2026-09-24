<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useCurrency } from '@/composables/useCurrency';
import { useLocale } from '@/composables/useLocale';
import type { PageProps, Property } from '@/types';

const props = defineProps<{ property: Property; favorite?: boolean }>();
const page = usePage<PageProps>();
const compared = ref(false);
const { formatPrice } = useCurrency();
const { localized, t } = useLocale();
const title = computed(() => localized(props.property, 'title', props.property.title));
const address = computed(() => localized(props.property, 'address', props.property.address));
const district = computed(() => localized(props.property, 'district', props.property.district));
const typeName = computed(() => localized(props.property.type, 'name', props.property.type.name));
const favoriteToggle = () => page.props.auth.user
    ? router.visit(`/favorites/${props.property.slug}`, { method: props.favorite ? 'delete' : 'post', preserveScroll: true })
    : router.visit('/login');
const compareToggle = () => {
    const items: Property[] = JSON.parse(localStorage.getItem('nova_compare') || '[]');
    const exists = items.some((item) => item.id === props.property.id);
    const next = exists ? items.filter((item) => item.id !== props.property.id) : [...items, props.property].slice(-4);
    localStorage.setItem('nova_compare', JSON.stringify(next));
    compared.value = !exists;
};
</script>

<template>
    <article class="card group flex h-full min-w-0 flex-col overflow-hidden">
        <div class="relative overflow-hidden">
            <img :src="property.cover_url" :alt="title" class="h-60 w-full object-cover transition duration-500 group-hover:scale-[1.03]">
            <span class="status absolute left-4 top-4">{{ property.operation === 'sale' ? t('property.sale') : t('property.rent') }}</span>
            <button class="absolute right-4 top-4 grid h-10 w-10 place-items-center rounded-full bg-white/95 text-lg shadow" :aria-label="t(favorite ? 'property.saved' : 'property.favorite')" @click="favoriteToggle">{{ favorite ? '♥' : '♡' }}</button>
        </div>
        <div class="flex min-w-0 flex-1 flex-col p-5">
            <div class="truncate text-xs font-bold uppercase tracking-wider text-[#8b6b49]" :title="`${typeName} · ${district}`">{{ typeName }} · {{ district }}</div>
            <Link :href="`/properties/${property.slug}`" class="min-w-0">
                <h3 class="mt-2 min-h-[3.4rem] overflow-hidden text-xl leading-snug group-hover:text-[#174c43] [display:-webkit-box] [-webkit-box-orient:vertical] [-webkit-line-clamp:2]" :title="title">{{ title }}</h3>
            </Link>
            <p class="mt-2 truncate text-sm text-stone-500" :title="address">{{ address }}</p>
            <div class="mt-auto grid gap-3 border-t border-stone-200 pt-4">
                <div class="flex min-w-0 flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                    <strong class="whitespace-nowrap text-xl text-[#174c43]">{{ formatPrice(property.price) }}<small v-if="property.operation === 'rent'" class="ml-1 text-xs font-normal">/ {{ t('property.month') }}</small></strong>
                    <span class="whitespace-nowrap text-sm text-stone-500">{{ property.rooms }} {{ t('property.rooms') }} · {{ property.area }} m²</span>
                </div>
                <button class="w-fit text-xs font-bold text-[#174c43]" @click="compareToggle">{{ compared ? `✓ ${t('property.compared')}` : `+ ${t('property.compare')}` }}</button>
            </div>
        </div>
    </article>
</template>
