<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="currency in currencies" :value="currency.id"
                    :selected="currency.id === selected"
                    :data-content="'<img style=\'height: 20px;\' src=' + (window.library.icons[currency.icon] ? window.library.icons[currency.icon].url : '') + '>' + ' ' + currency.name_i18n.en">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["currencies", "value"],

        data() {
            return {
                window: window,
                selected: this.$props.value
            };
        },

        mounted() {
            window.$(this.$el.querySelector('select')).selectpicker('refresh');
        },

        updated() {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        watch: {
            selected: function (value) {
                this.$emit("input", value);
            },

            value: function (value) {
                this.selected = value;
            }
        }
    };
</script>

<style scoped>

</style>
