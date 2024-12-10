import {
    Box,
    Button,
    Checkbox,
    Form,
    FormGroup,
    Panel,
} from '@bigcommerce/big-design';
import { useEffect, useState } from 'react';

export default function ExportFieldSection(props) {
    const initialValues = {
        id: true,
        customer_id: true,
        order_id: true,
        code: true,
        from_name: true,
        from_email: true,
        to_name: true,
        to_email: true,
        amount: true,
        balance: true,
        status: true,
        template: true,
        message: true,
        purchase_date: true,
        expiry_date: true,
        currency_code: true,
    };

    const [values, setValues] = useState(initialValues);

    const handleCheckboxChange = (e) => {
        const { name, checked } = e.target;
        setValues({
            ...values,
            [name]: checked,
        });

        props.setFields({
            ...values,
            [name]: checked,
        });
    };

    useEffect(() => {
        props.setFields(values);
    }, []);

    return (
        <>
            <Panel
                marginTop="xxLarge"
                description="Please select all the fields for the export. Data for all selected gift certificates will be exported as a CSV file."
                header="Fields"
            >
                <Form>
                    <FormGroup>
                        <Checkbox
                            label="id"
                            name="id"
                            checked={values.id}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="customer_id"
                            name="customer_id"
                            checked={values.customer_id}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="order_id"
                            name="order_id"
                            checked={values.order_id}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="code"
                            name="code"
                            checked={values.code}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="from_name"
                            name="from_name"
                            checked={values.from_name}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="from_email"
                            name="from_email"
                            checked={values.from_email}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="to_name"
                            name="to_name"
                            checked={values.to_name}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="to_email"
                            name="to_email"
                            checked={values.to_email}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="amount"
                            name="amount"
                            checked={values.amount}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="balance"
                            name="balance"
                            checked={values.balance}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="status"
                            name="status"
                            checked={values.status}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="template"
                            name="template"
                            checked={values.template}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="message"
                            name="message"
                            checked={values.message}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="purchase_date"
                            name="purchase_date"
                            checked={values.purchase_date}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="expiry_date"
                            name="expiry_date"
                            checked={values.expiry_date}
                            onChange={handleCheckboxChange}
                        />
                        <Checkbox
                            label="currency_code"
                            name="currency_code"
                            checked={values.currency_code}
                            onChange={handleCheckboxChange}
                        />
                    </FormGroup>
                </Form>

                <Box marginTop="xxLarge">
                    <Button variant="primary" onClick={props.onSubmit}>
                        Start Export
                    </Button>
                </Box>
            </Panel>
        </>
    );
}
