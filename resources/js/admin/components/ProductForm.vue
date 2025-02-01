<script>
    import BaseForm from '../../main/components/forms/base/BaseForm.vue';

    export default {
        extends: BaseForm,

        data() {
            return {
                fields: {
                    vinil_cost: "0",
                    impresion_cost: "0",
                    indirect_cost: "0",
                    costo_total: "0",
                    costo_venta: "0"
                }
            };
        },
        watch: {
            // Observa los cambios en los costos y recalcula `costo_total`
            fields: {
                deep: true,
                handler() {
                    const vinil = parseFloat(this.fields.vinil_cost) || 0;
                    const impresion = parseFloat(this.fields.impresion_cost) || 0;
                    const indirecto = parseFloat(this.fields.indirect_cost) || 0;
                    this.fields.costo_total = (vinil + impresion + indirecto).toFixed(2);
                }
            }
        }

    }
</script>
