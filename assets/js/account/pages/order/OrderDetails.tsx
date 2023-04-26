import {
	Divider,
	FormLabel,
	Heading,
	Stack,
	Tag,
	Text,
	useToast,
} from '@chakra-ui/react';
import { sprintf, __ } from '@wordpress/i18n';
import React from 'react';
import { Col, Row } from 'react-grid-system';
import { useQuery } from 'react-query';
import { useParams } from 'react-router-dom';
import FullScreenLoader from '../../../back-end/components/layout/FullScreenLoader';
import urls from '../../../back-end/constants/urls';
import { OrderItemSchema, OrderSchema } from '../../../back-end/schemas';
import API from '../../../back-end/utils/api';
import { getLocalTime } from '../../../back-end/utils/utils';

const OrderDetails: React.FC = () => {
	const ordersAPI = new API(urls.orders);
	const orderItemsAPI = new API(urls.order_items);

	const { orderId }: any = useParams();
	const toast = useToast();

	const orderQuery = useQuery<OrderSchema>([`myOrder${orderId}`, orderId], () =>
		ordersAPI.get(orderId)
	);

	const orderItemsQuery = useQuery(
		'orderItemsList',
		() =>
			orderItemsAPI.list({
				order_id: orderId,
			}),
		{
			onError: (error: any) => {
				toast({
					description: `${error.response?.data?.message}`,
					isClosable: true,
					status: 'error',
				});
			},
		}
	);
	if (orderQuery.isSuccess) {
		return (
			<Stack
				direction="column"
				spacing="8"
				width="full"
				className="mto-orders-detail-wrapper">
				<Heading as="h4" size="md" fontWeight="bold" color="primary.900">
					{sprintf(__('Order #%s', 'masteriyo'), orderQuery?.data?.id)}
				</Heading>
				<Row gutterWidth={30}>
					<Col>
						<FormLabel>{__('Date Created', 'masteriyo')}</FormLabel>
						<Text fontSize="sm">
							{getLocalTime(orderQuery?.data?.date_created).toString()}
						</Text>
					</Col>
					<Col>
						<Row gutterWidth={30}>
							<Col>
								<FormLabel>{__('First Name', 'masteriyo')}</FormLabel>
								<Text>{orderQuery?.data?.billing?.first_name}</Text>
							</Col>
							<Col>
								<FormLabel>{__('Last Name', 'masteriyo')}</FormLabel>
								<Text>{orderQuery?.data?.billing?.last_name}</Text>
							</Col>
						</Row>
					</Col>
				</Row>

				<Row gutterWidth={30}>
					<Col>
						<FormLabel>{__('Status', 'masteriyo')}</FormLabel>
						<Tag>{orderQuery?.data?.status}</Tag>
					</Col>
					<Col>
						<FormLabel>{__('Email', 'masteriyo')}</FormLabel>
						<Text>{orderQuery?.data?.billing?.email}</Text>
					</Col>
				</Row>
				<Row gutterWidth={30}>
					<Col>
						<FormLabel>{__('Payment Method', 'masteriyo')}</FormLabel>
						<Text>{orderQuery?.data?.payment_method}</Text>
					</Col>
					<Col>
						<FormLabel>{__('Transaction ID', 'masteriyo')}</FormLabel>
						<Text>{orderQuery?.data?.transaction_id}</Text>
					</Col>
				</Row>

				<Divider />

				{orderItemsQuery.isSuccess &&
					orderItemsQuery.data.map((orderItem: OrderItemSchema) => (
						<Stack key={orderItem.id} direction="row" spacing="6">
							<Text flexGrow={1} fontWeight="semibold">
								{orderItem.name}
							</Text>
							<Text fontSize="sm" fontWeight="medium" color="gray.600">
								x {orderItem.quantity}
							</Text>
							<Text fontSize="sm" fontWeight="medium" color="gray.600">
								{orderQuery?.data?.formatted_total}
							</Text>
						</Stack>
					))}
			</Stack>
		);
	}

	return <FullScreenLoader />;
};

export default OrderDetails;
