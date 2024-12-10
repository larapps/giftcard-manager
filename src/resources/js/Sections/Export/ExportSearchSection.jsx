import { Form, FormGroup, Input, Panel } from '@bigcommerce/big-design';
import { useEffect, useState } from 'react';

export default function ExportSearchSection(props) {
    const initialValues = {
        min_id: '',
        max_id: '',
        code: '',
        order_id: '',
        to_name: '',
        to_email: '',
        from_name: '',
        from_email: '',
    };

    const [values, setValues] = useState(initialValues);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setValues({
            ...values,
            [name]: value,
        });

        props.setFilters({
            ...values,
            [name]: value,
        });
    };

    useEffect(() => {
        props.setFilters(values);
    }, []);

    return (
        <>
            <Panel
                description="If you wish to apply filters to the data export, please provide them here."
                header="Filters"
            >
                <Form fullWidth={true}>
                    <FormGroup>
                        <Input
                            description="Bigcommerce minimum ID to filter"
                            label="Min Id"
                            type="number"
                            name="min_id"
                            value={values.min_id}
                            onChange={handleInputChange}
                        />
                        <Input
                            description="Bigcommerce maximum ID to filter"
                            label="Max Id"
                            type="number"
                            name="max_id"
                            value={values.max_id}
                            onChange={handleInputChange}
                        />
                        <Input
                            description="Gift certificate code"
                            label="Code"
                            type="text"
                            name="code"
                            value={values.code}
                            onChange={handleInputChange}
                        />
                    </FormGroup>
                    <FormGroup>
                        <Input
                            description="Order ID to filter"
                            label="Order ID"
                            type="number"
                            name="order_id"
                            value={values.order_id}
                            onChange={handleInputChange}
                        />
                        <Input
                            description="To Name to filter"
                            label="To Name"
                            type="text"
                            name="to_name"
                            value={values.to_name}
                            onChange={handleInputChange}
                        />
                        <Input
                            description="To Email to filter"
                            label="To Email"
                            type="email"
                            name="to_email"
                            value={values.to_email}
                            onChange={handleInputChange}
                        />
                    </FormGroup>
                    <FormGroup>
                        <Input
                            description="From Name to filter"
                            label="From Name"
                            type="text"
                            name="from_name"
                            value={values.from_name}
                            onChange={handleInputChange}
                        />
                        <Input
                            description="From Email to filter"
                            label="From Email"
                            type="email"
                            name="from_email"
                            value={values.from_email}
                            onChange={handleInputChange}
                        />
                    </FormGroup>
                </Form>
            </Panel>
        </>
    );
}
