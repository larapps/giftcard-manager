import { Worksheet } from '@bigcommerce/big-design';

export default function WorksheetPreview(props) {
    const disabledRows = [1, 2];

    return (
        <Worksheet
            columns={props.headerData}
            disabledRows={disabledRows}
            items={props.previewData}
            onChange={(items) => items}
        />
    );
}
