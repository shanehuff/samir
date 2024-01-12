<script setup>
import AppLayout from '@/Layouts/HeadlessAppLayout.vue';
import NavLink from "../../Components/NavLink.vue";

defineProps({
    incomes: Array
})
</script>

<template>
    <AppLayout title="Deals">
        <template #header>
            <div class="flex justify-center">
                <h2 class="text-xl text-gray-800 leading-tight dark:text-white">
                    Incomes 💰
                </h2>
            </div>
        </template>

        <div class="p-6 max-w-7xl mx-auto">
            <div class="grid gap-6 mb-8">
                <div v-for="income in incomes"
                     class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4 flex justify-between">
                      <span class="text-sm font-semibold text-gray-700 dark:text-gray-400">
                        🎉 {{ income.day }}
                      </span>

                      <span v-if="parseInt(income.income) > 0" class="text-sm font-semibold text-green-700 dark:text-green-500">
                          + {{ income.income }} 💰
                      </span>

                      <span v-else class="text-sm font-semibold text-red-700 dark:text-red-500">
                        {{ income.income }} 💸
                      </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>