<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
})

</script>

<template>

    <Head title="Welcome" />

    <!-- Background -->
    <div
        class="relative min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 text-slate-700 dark:from-zinc-950 dark:via-zinc-900 dark:to-zinc-900 dark:text-zinc-200">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -top-24 -left-32 size-[35rem] rounded-full bg-indigo-500/10 blur-3xl dark:bg-indigo-400/10" />
            <div
                class="absolute -bottom-24 -right-32 size-[35rem] rounded-full bg-fuchsia-500/10 blur-3xl dark:bg-fuchsia-400/10" />
        </div>

        <!-- NAVBAR -->
        <header class="relative z-10">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
                <div class="flex items-center gap-3">
                    <!-- Logo -->
                    <div
                        class="flex size-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md dark:bg-indigo-500">
                        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7V3m-8 4V3m-2 8h12M5 21h14a2 2 0 0 0 2-2v-8H3v8a2 2 0 0 0 2 2Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-slate-900 dark:text-zinc-100">EMS</p>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Employee Management System</p>
                    </div>
                </div>

                <div v-if="canLogin" class="flex items-center gap-2">
                    <template v-if="$page.props.auth?.user">
                        <Link :href="$page.props.auth.is_admin ? route('admin.dashboard') : route('employee.dashboard')"
                            class="rounded-xl border border-transparent bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                        Dashboard
                        </Link>

                        <Link as="button" method="post" :href="route('logout')"
                            class="rounded-xl border px-4 py-2 text-sm font-medium hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-zinc-700 dark:hover:bg-zinc-800">
                        Logout
                        </Link>
                    </template>

                    <template v-else>
                        <Link :href="route('login')"
                            class="rounded-xl border px-4 py-2 text-sm font-medium hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-zinc-700 dark:hover:bg-zinc-800">
                        Log in
                        </Link>
                        <Link v-if="canRegister" :href="route('register')"
                            class="rounded-xl border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        Register
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- HERO -->
        <main class="relative z-10">
            <section class="mx-auto grid max-w-7xl items-center gap-10 px-6 py-10 md:grid-cols-2 md:py-16">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 md:text-5xl dark:text-zinc-50">
                        Run your people ops <span class="text-indigo-600 dark:text-indigo-400">beautifully</span>
                    </h1>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-slate-600 dark:text-zinc-400">
                        Manage employees, attendance, and leaves in one place. Search, filter, and update with ease
                        using
                        Laravel, Inertia, and Vue.
                    </p>
                </div>

                <!-- Illustration -->

            </section>

            <!-- FEATURES -->
            <section class="mx-auto max-w-7xl px-6 pb-16">
                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        class="rounded-2xl border bg-white/80 p-6 shadow-sm backdrop-blur dark:border-zinc-700 dark:bg-zinc-800/60">
                        <div
                            class="mb-3 flex size-10 items-center justify-center rounded-lg bg-indigo-600 text-white dark:bg-indigo-500">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold">Employee Directory</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            Create, search, paginate and edit employee records with validation synced to Users.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border bg-white/80 p-6 shadow-sm backdrop-blur dark:border-zinc-700 dark:bg-zinc-800/60">
                        <div
                            class="mb-3 flex size-10 items-center justify-center rounded-lg bg-indigo-600 text-white dark:bg-indigo-500">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3M4 11h16M5 21h14a2 2 0 0 0 2-2v-8H3v8a2 2 0 0 0 2 2Z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold">Attendance & CSV</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            Filter by date, edit time-in/out, import scans; weekends auto-skipped, absences
                            auto-inferred.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border bg-white/80 p-6 shadow-sm backdrop-blur dark:border-zinc-700 dark:bg-zinc-800/60">
                        <div
                            class="mb-3 flex size-10 items-center justify-center rounded-lg bg-indigo-600 text-white dark:bg-indigo-500">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 10h18M7 15h10M12 3v3m0 15v-3" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold">Role-aware Access</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">
                            Admin vs Employee dashboards with Inertia-powered navigation and Ziggy routes.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer
            class="relative z-10 border-t px-6 py-6 text-center text-xs text-slate-500 dark:border-zinc-800 dark:text-zinc-500">
            Laravel v{{ laravelVersion }} • PHP v{{ phpVersion }} • EMS Starter
        </footer>
    </div>
</template>
