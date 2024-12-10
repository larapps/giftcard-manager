import {
    Button,
    FileUploader,
    Form,
    FormGroup,
    Small,
} from '@bigcommerce/big-design';
import { useState } from 'react';

export default function FileUploadForm(props) {
    const [files, setFiles] = useState([]);

    const [errorMsg, setErrorMsg] = useState();

    const parseCSV = (str) => {
        const arr = [];
        var quote = false;
        for (var row = 0, col = 0, c = 0; c < str.length; c++) {
            var cc = str[c],
                nc = str[c + 1];
            arr[row] = arr[row] || [];
            arr[row][col] = arr[row][col] || '';

            if (cc == '"' && quote && nc == '"') {
                arr[row][col] += cc;
                ++c;
                continue;
            }
            if (cc == '"') {
                quote = !quote;
                continue;
            }
            if (cc == ',' && !quote) {
                ++col;
                continue;
            }
            if (cc == '\n' && !quote) {
                ++row;
                col = 0;
                continue;
            }

            arr[row][col] += cc;
        }
        return arr;
    };

    const validateFileSize = (file) => {
        const MB = 1024 * 1024;

        if (file.type !== 'text/csv') {
            setErrorMsg('File needs to be in CSV format.');
            return false;
        }

        if (file.size > MB) {
            setErrorMsg('File size exceeds the limit of 2MB.');
            return false;
        }

        setErrorMsg('');
        return true;
    };

    const showPreview = (event) => {
        event.preventDefault();
        const file = files[0];
        props.changeFile(files);
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                let content = e?.target?.result;

                if (content) {
                    const rows = content.split('\r\n');
                    const headerData = rows[0].split(',');
                    const previewArray = rows
                        .slice(1, 3)
                        .map((row) => row.split(','));
                    const parsedArray = parseCSV(previewArray.join('\n'));

                    const previewData = parsedArray.map((row, index) => ({
                        id: index + 1,
                        ...Object.fromEntries(
                            headerData.map((header, i) => [header, row[i]]),
                        ),
                    }));

                    const columns = headerData.map((header) => ({
                        hash: header,
                        header,
                        width: 200,
                    }));

                    props.setPreviewData(previewData);
                    props.setHeaderData(columns);
                    props.nextStep();
                }
            };
            reader.readAsText(file);
        }
    };

    return (
        <>
            <Form fullWidth={true}>
                <FormGroup>
                    <FileUploader
                        dropzoneConfig={{
                            label: 'Drag and drop a csv file(2 MB size limit)',
                        }}
                        files={files}
                        label="Upload files"
                        onFilesChange={setFiles}
                        required
                        error={errorMsg}
                        accept="text/csv"
                        validators={[
                            {
                                validator: validateFileSize,
                                type: 'file-size',
                            },
                        ]}
                    />
                </FormGroup>
                <FormGroup>
                    <Small>
                        To update existing gift certificates, the corresponding
                        gift certificate IDs in the file (found in the "ID"
                        column) will be used to match and update the records
                        during the import process.
                    </Small>
                </FormGroup>
                <Button
                    actionType="normal"
                    isLoading={false}
                    variant="primary"
                    onClick={showPreview}
                >
                    Preview
                </Button>
            </Form>
        </>
    );
}
