<script setup>
import AppLayout from '@/Layouts/HeadlessAppLayout.vue';
import {ref} from "vue";
import DialogModal from "../../Components/DialogModal.vue";
import SecondaryButton from "../../Components/SecondaryButton.vue";

defineProps({
    netProfit: String,
    fee: String,
    dealsCount: Number,
    upTime: String,
    incomes: String,
    incomesPerYear: String,
    revenue: String,
    avgRoe: String,
    totalBuy: String,
    totalSell: String,
})

const isShowIncomes = ref(false);

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white text-center">
                Dashboard
            </h2>
        </template>

        <div class="p-6 max-w-7xl mx-auto">
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-2">
                <div
                        class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Revenue & Estimated incomes per year (VND)
                            </p>
                            <p class="flex justify-between font-semibold text-gray-700 dark:text-gray-200">
                                <span @click="isShowIncomes = true" class="cursor-pointer text-green-500">+{{ revenue }} 💰</span>

                                <span class="text-blue-500 flex-shrink-0">+{{ incomesPerYear }} 💨</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Average ROI per deal
                            </p>
                            <p class="flex justify-between font-semibold text-gray-700 dark:text-gray-200">
                                <span class="text-green-500">{{ avgRoe }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Total Buy & Total Sell
                            </p>
                            <p class="flex justify-between font-semibold text-gray-700 dark:text-gray-200">
                                <span class="text-green-500">{{ totalBuy }} </span>
                                <span class="text-red-500">{{ totalSell }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div
                        class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"
                >
                    <div class="p-4">
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Deals & Uptime
                            </p>
                            <p class="flex justify-between font-semibold text-gray-700 dark:text-gray-200">
                                <span class="flex-shrink-0">{{ dealsCount }} 🤝</span>
                                <span class="flex-shrink-0">{{ upTime }} ⏱️</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <DialogModal :show="isShowIncomes">
            <template #content>
                <div
                    class="min-w-0 rounded-lg shadow-xs overflow-hidden bg-white"
                >
                    <div class="p-2">
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600">
                                Trading Net Profits
                            </p>
                            <p class="font-semibold text-gray-700">
                                <a href="/deals">
                                    <span class="text-green-500">+{{ netProfit }} 💰</span>
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="p-2">
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600">
                                Trading Fees
                            </p>
                            <p class="font-semibold text-gray-700">
                                <span class="text-red-500">-{{ fee }} 💸</span>
                            </p>
                        </div>
                    </div>

                    <div class="p-2">
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600">
                                Funding Fees
                            </p>
                            <p class="font-semibold text-gray-700">
                                <a href="/incomes">
                                    <span class="text-green-500">+{{ incomes }} 💰</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="isShowIncomes = false">
                    Close
                </SecondaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>