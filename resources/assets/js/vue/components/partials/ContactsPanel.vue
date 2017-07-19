<template>
    <div class="form-wrapper">
        <div class="contacts-panel-header new-clients-header">
            Contacts
        </div>
        <div class="form-panel-body">
            <div v-for="(form, index) in forms" class="contact-form">
                <hr v-if="index">
                <div class="flex-grid">
                    <div class="col">
                        <span>First Name <input :name.once="`contacts[${index}][first_name]`" type="text" v-model="form.first_name"><br></span>
                    </div>
                    <div class="col">
                        <span>Last Name <input :name.once="`contacts[${index}][last_name]`" type="text" v-model="form.last_name"><br></span>
                    </div>
                    <div class="col item">
                        <span>Job Position <input :name.once="`contacts[${index}][job_position]`" type="text" v-model="form.job_position"><br></span>
                    </div>
                </div>
                <div class="flex-grid">
                    <div class="col">
                        <span>Email <input :name.once="`contacts[${index}][email]`" type="text" v-model="form.email"><br></span>
                    </div>
                    <div class="col">
                        <span>Phone <input :name.once="`contacts[${index}][phone]`" type="text" v-model="form.phone"><br></span>
                    </div>
                </div>
            </div>
            <div class="new-client-link">
                <a @click.prevent="add">+ Add New Contact</a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'contacts'
        ],
        data() {
            let forms = [];

            if (this.contacts) {
                this.contacts.forEach(contact => {
                    forms.push({
                        first_name: contact.first_name,
                        last_name: contact.last_name,
                        email: contact.email,
                        phone: contact.phone
                    });
                });
            }
            else {
                forms.push({});
            }

            return {
                forms
            };
        },
        methods: {
            add() {
                this.forms.push({});
            }
        }
    }
</script>

<style>
    input[readonly] {
        cursor: no-drop;
    }
</style>