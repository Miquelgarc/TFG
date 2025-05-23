<script setup lang="ts">
import BenefitCard from '../components/BenefitCard.vue';
import { usePage, Link } from '@inertiajs/vue3';

interface AuthProps {
    user?: any;
}
interface PageProps {
    auth: AuthProps;
    [key: string]: any;
}

const page = usePage<PageProps>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0A0A0A] to-gray-800 text-white px-6 py-12">
        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <header class="text-center mb-16">
                <h1 class="text-4xl md:text-6xl font-bold mb-4 animate-fade-in">
                    Benvinguts a <span class="text-yellow-300"></span>
                </h1>
                <p class="text-xl md:text-2xl text-white/80 animate-slide-in">
                        Comença a guanyar amb el nostre programa d'afiliats
                </p>
            <nav class="flex items-center justify-center gap-4 mt-8">
                <Link
                    v-if="page.props.auth.user"
                    :href="route('infoAfiliat')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Tauler
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Inicia sessió
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Registra't
                    </Link>
                </template>
            </nav>
            </header>
        <!-- Descripció principal -->
        <p class="text-lg md:text-xl text-white/70 text-center max-w-3xl mx-auto mb-12 animate-fade-in">
                El nostre programa d'afiliats et permet guanyar comissions recomanant els nostres serveis. Sense inversió inicial, amb seguiment en temps real i pagaments ràpids. Uneix-te i comença a generar ingressos avui mateix!
        </p>
            <!-- Esquema / Pasos -->
            <section class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="bg-white/10 p-6 rounded-2xl shadow-lg backdrop-blur-lg hover:scale-105 transition">
                    <h3 class="text-2xl font-semibold mb-2 text-yellow-300">1. Comparteix els teus enllaços</h3>
                    <p>Registra't com a afiliat i comparteix el teu enllaç únic.</p>
                </div>
                <div class="bg-white/10 p-6 rounded-2xl shadow-lg backdrop-blur-lg hover:scale-105 transition">
                    <h3 class="text-2xl font-semibold mb-2 text-yellow-300">2. Convida la teva xarxa</h3>
                    <p>Publica l'enllaç a xarxes socials, grups o blogs per atraure usuaris.</p>
                </div>
                <div class="bg-white/10 p-6 rounded-2xl shadow-lg backdrop-blur-lg hover:scale-105 transition">
                    <h3 class="text-2xl font-semibold mb-2 text-yellow-300">3. Guanya comissions</h3>
                    <p>Per cada reserva realitzada des del teu enllaç, guanyes una comissió automàtica.</p>
                </div>
            </section>

            <!-- Secció dinàmica de beneficis -->
            <section class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-6">Per què unir-t'hi?</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <BenefitCard
                        title="Sense inversió inicial"
                        text="Comença a guanyar sense necessitat de tenir una propietat o pagar quotes."
                        icon="💸"
                    />
                    <BenefitCard
                        title="Seguiment en temps real"
                        text="Monitora els teus clics, conversions i comissions des del teu panell personal."
                        icon="📊"
                    />
                    <BenefitCard
                        title="Pagaments ràpids"
                        text="Rep els teus pagaments mensualment al teu compte bancari o PayPal."
                        icon="⚡"
                    />
                    <BenefitCard
                        title="Material promocional"
                        text="Accedeix a imatges, bàners i textos per ajudar-te a promocionar."
                        icon="📢"
                    />
                </div>
            </section>

            <!-- CTA -->
            <div class="text-center">
                <router-link
                    to="/register"
                    class="bg-yellow-400 text-black font-bold px-8 py-4 rounded-full hover:bg-yellow-300 transition"
                >
                    Uneix-te ara i comença a guanyar!
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slide-in {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.animate-fade-in {
    animation: fade-in 1s ease-out forwards;
}
.animate-slide-in {
    animation: slide-in 1.2s ease-out forwards;
}
</style>
