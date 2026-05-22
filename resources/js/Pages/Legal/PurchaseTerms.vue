<script setup>
    import Layout from '@/Layouts/Layout.vue';
    import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Layout>
        <Head title="Condiciones de compra" />

        <main class="min-h-screen bg-[#0F172A] text-white">
            <section class="mx-auto max-w-5xl px-6 py-12 md:py-16">
                <p class="text-sm uppercase tracking-[0.35em] text-yellow-300">Legal</p>
                <h1 class="mt-3 text-3xl font-bold md:text-4xl">Condiciones generales de venta</h1>
                <p class="mt-3 text-sm text-slate-400">https://filmness.onrender.com/</p>

                <div class="mt-8 space-y-6">
                    <article class="rounded-2xl border border-white/10 bg-slate-900/70 p-6">
                        <h2 class="text-xl font-semibold text-white">1. Informacion general</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            La titularidad de este sitio web la ostenta Iker Escartin Escanero, con NIF 27351094T.
                            Datos de contacto: direccion [PENDIENTE], telefono 655598365 y email ikeree2005@gmail.com.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Este documento regula las condiciones de uso del sitio y la compra o adquisicion de
                            productos y/o servicios. La actividad desarrollada por FilmNess a traves del sitio web
                            comprende la comercializacion y distribucion de entradas de cine.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            El uso del sitio implica aceptar estas condiciones, el aviso legal, la politica de cookies
                            y la politica de privacidad. FilmNess puede modificar estas condiciones, por lo que el
                            usuario debe revisarlas antes de cada compra.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">2. El usuario</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            El acceso, navegacion y uso del sitio confiere la condicion de usuario y supone la
                            aceptacion de estas condiciones y sus posibles modificaciones.
                        </p>
                        <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-7 text-slate-300">
                            <li>Usar el sitio solo para consultas y compras legalmente validas.</li>
                            <li>No realizar compras falsas o fraudulentas.</li>
                            <li>Facilitar datos de contacto veraces y licitos.</li>
                        </ul>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            El usuario declara ser mayor de 18 anos y tener capacidad legal para contratar.
                            El sitio esta orientado principalmente a usuarios residentes en Espana.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">3. Proceso de compra o adquisicion</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Los usuarios debidamente registrados pueden comprar siguiendo el flujo online hasta pulsar
                            "Pagar". Durante el proceso, podran revisar y corregir sus datos antes del pago.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            El usuario recibira confirmacion por correo electronico y, en su caso, informacion adicional
                            sobre su pedido. Tras finalizar la compra, se podra emitir factura electronica.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Las comunicaciones, ordenes y pagos podran archivarse como medio de prueba, respetando RGPD
                            y LOPD-GDD.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">4. Disponibilidad</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Todos los pedidos estan sujetos a disponibilidad y a posibles causas de fuerza mayor.
                            Si no hay disponibilidad, FilmNess informara al usuario y reembolsara, en su caso,
                            los importes abonados.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">5. Precios y pago</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Los precios se muestran en euros e incluyen impuestos salvo indicacion legal distinta.
                            Salvo indicacion expresa, los gastos de envio no se incluyen y se anaden durante la compra.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            El medio de pago aceptado es tarjeta de credito o debito.
                            El sitio utiliza SSL para proteger los datos de pago.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Las tarjetas estan sujetas a autorizacion de su entidad emisora. Al pulsar "Pagar",
                            el usuario confirma que el metodo de pago utilizado es suyo.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">6. Entrega</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Cuando proceda entrega fisica, el ambito territorial previsto es Espana (Peninsula,
                            Baleares, Canarias, Ceuta y Melilla).
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Salvo circunstancias excepcionales, la entrega se realizara dentro del plazo indicado
                            y, en todo caso, en un maximo de 30 dias naturales desde la confirmacion del pedido.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Si la entrega no puede realizarse por causa imputable al usuario, el pedido podra devolverse
                            al almacen. Si pasan 30 dias sin entrega por causa no imputable a FilmNess, se entendera
                            desistido el contrato y se procedera al reembolso segun la normativa aplicable.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            En envios a Canarias, Ceuta y Melilla podran aplicarse impuestos y derechos de aduana
                            conforme a normativa vigente.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">7. Medios tecnicos para corregir errores</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Si el usuario detecta errores en los datos de compra, podra corregirlos durante el proceso
                            antes de pagar o contactando por los medios habilitados por FilmNess.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">8. Devoluciones</h2>
                        <h3 class="mt-4 text-base font-semibold text-white">Derecho de desistimiento</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            El usuario dispone de 14 dias naturales para desistir de la compra sin necesidad de
                            justificacion, en los terminos previstos por la normativa de consumo.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            En caso de desistimiento, FilmNess reembolsara los importes abonados en un maximo de
                            14 dias naturales desde la comunicacion del desistimiento, pudiendo retener el reembolso
                            hasta recibir los bienes o prueba de devolucion.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            El usuario asume el coste directo de devolucion cuando proceda. Existen excepciones al
                            derecho de desistimiento segun el articulo 103 del RDL 1/2007.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Modelo de formulario de desistimiento:
                            <a
                                href="https://filmness.onrender.com/modelodesistimiento/"
                                class="text-yellow-300 underline hover:text-yellow-200"
                            >
                                https://filmness.onrender.com/modelodesistimiento/
                            </a>
                        </p>

                        <h3 class="mt-4 text-base font-semibold text-white">Devolucion por defecto o error en envio</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Si el producto no se ajusta al pedido, el usuario debe comunicarlo a FilmNess.
                            Tras revisar el producto devuelto, se informara si procede reembolso o sustitucion.
                        </p>

                        <h3 class="mt-4 text-base font-semibold text-white">Garantias</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            El usuario consumidor tiene las garantias legales sobre los productos adquiridos,
                            respondiendo FilmNess por faltas de conformidad en el plazo legal vigente.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">9. Exoneracion de responsabilidad</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Salvo disposicion legal en contrario, FilmNess no respondera por perdidas indirectas,
                            lucro cesante u otras perdidas no razonablemente previsibles al formalizar el contrato.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Tampoco respondera por retrasos o incumplimientos debidos a fuerza mayor (huelgas,
                            desastres naturales, conflictos, fallos de telecomunicaciones o actos de autoridad,
                            entre otros supuestos analogos).
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">10. Comunicaciones por escrito y notificaciones</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            El usuario acepta que gran parte de las comunicaciones con FilmNess sean electronicas
                            (correo electronico o avisos en el sitio web), con validez legal de comunicacion escrita.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">11. Renuncia</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            La falta de ejercicio de un derecho por parte de FilmNess no implica renuncia al mismo
                            ni a otros derechos derivados del contrato o de estas condiciones.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">12. Nulidad</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Si alguna clausula fuese declarada nula, las demas permaneceran vigentes.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">13. Acuerdo completo</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Estas condiciones constituyen el acuerdo integro entre el usuario y FilmNess en relacion
                            con la compraventa, sustituyendo pactos o promesas anteriores.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">14. Proteccion de datos</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            Los datos personales facilitados durante la compra se trataran conforme a la politica
                            de privacidad y normativa aplicable de proteccion de datos.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">15. Legislacion aplicable y jurisdiccion</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            El acceso y uso del sitio, asi como los contratos de compra, se rigen por la legislacion
                            espanola. Las controversias se someteran a los juzgados y tribunales espanoles.
                        </p>

                        <h2 class="mt-8 text-xl font-semibold text-white">16. Quejas y reclamaciones</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-300">
                            El usuario puede presentar quejas y reclamaciones a traves de los datos de contacto
                            facilitados en estas condiciones.
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Tambien puede acudir a la plataforma europea de resolucion de litigios en linea:
                            <a
                                href="https://ec.europa.eu/consumers/odr/"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-yellow-300 underline hover:text-yellow-200"
                            >
                                https://ec.europa.eu/consumers/odr/
                            </a>
                            .
                        </p>
                        <p class="mt-2 text-sm leading-7 text-slate-300">
                            Documento base generado con apoyo de la plantilla de textos-legales.edgartamarit.com
                            el 22/05/2026.
                        </p>
                    </article>
                </div>
            </section>
        </main>
    </Layout>
</template>
