import { Table } from '@bigcommerce/big-design';
import axios from 'axios';
import { useState } from 'react';

export default function ErrorTable(data) {
    // console.log("data da"+JSON.stringify(data.data));

    const [currentPage, setCurrentPage] = useState(1);
    const [itemsPerPageOptions] = useState([10, 20, 50]);
    const [itemsPerPage, setItemsPerPage] = useState(10);
    const [tableData, setTableData] = useState(data.data);
    const [fileId] = useState(data.fileId);

    const onItemsPerPageChange = (newRange) => {
        axios
            .get(
                '/gift-certificates/import/' +
                    fileId +
                    '/error-report?page=' +
                    1 +
                    '&limit=' +
                    newRange,
            )
            .then((response) => {
                if (response.status === 200) {
                    if (Object.values(response.data).length === 0) {
                        setTableData(response.data.data);
                        setCurrentPage(1);
                        setItemsPerPage(newRange);
                    }
                }
            });
    };

    const loadPage = (newPage) => {
        axios
            .get(
                '/gift-certificates/import/' +
                    fileId +
                    '/error-report?page=' +
                    newPage +
                    '&limit=' +
                    itemsPerPage,
            )
            .then((response) => {
                if (response.status === 200) {
                    if (Object.values(response.data).length > 0) {
                        // console.log("response", response);
                        setTableData(response.data.data);
                        setCurrentPage(newPage);
                    }
                }
            });
    };

    return (
        <>
            <Table
                columns={[
                    {
                        header: 'Code',
                        hash: 'code',
                        render: ({ code }) => code,
                    },
                    {
                        header: 'Error Message',
                        hash: 'error_message',
                        render: ({ table_output_reason }) =>
                            table_output_reason,
                    },
                ]}
                itemName="errors"
                keyField="id"
                items={tableData}
                pagination={{
                    currentPage,
                    totalItems: data.totalCount,
                    onPageChange: loadPage,
                    itemsPerPageOptions,
                    onItemsPerPageChange,
                    itemsPerPage,
                }}
                stickyHeader
            />
        </>
    );
}
