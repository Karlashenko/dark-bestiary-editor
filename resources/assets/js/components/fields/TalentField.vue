<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="talent in talents" :value="talent.id"
                    :selected="talent.id === selected"
                    :data-content="'<img style=\'height: 20px;\' src=' + (library.icons[talent.icon] ? library.icons[talent.icon].url : '') + '>' + ' ' + window.getPropertyByPath(talent, 'name_i18n.en')">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["talents", "value"],

        data() {
            return {
                window: window,
                library: window.library,
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
