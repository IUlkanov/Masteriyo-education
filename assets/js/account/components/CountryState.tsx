import {
	FormControl,
	FormErrorMessage,
	FormLabel,
	Select,
} from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React from 'react';
import { useFormContext, useWatch } from 'react-hook-form';
import { CountriesSchema, StatesSchema } from '../../back-end/schemas';
import { isEmpty } from '../../back-end/utils/utils';

interface Props {
	country: string;
	state: string;
	statesQuery: any;
	countriesQuery: any;
}
const CountryState: React.FC<Props> = (props) => {
	const { country, state, countriesQuery, statesQuery } = props;
	const watchSelectedCountry = useWatch({
		name: 'billing.country',
		defaultValue: country,
	});

	const {
		register,
		formState: { errors },
	} = useFormContext();

	const matchCountriesData = statesQuery?.data.filter(
		(statesData: StatesSchema) => statesData.country === watchSelectedCountry
	);

	return (
		<>
			<FormControl isInvalid={!!errors?.billing?.country}>
				<FormLabel>{__('Country', 'masteriyo')}</FormLabel>
				<Select {...register('billing.country')} defaultValue={country}>
					<option value="">{__('Select a country', 'masteriyo')}</option>
					{countriesQuery?.data.map((country: CountriesSchema) => (
						<option value={country.code} key={country.code}>
							{country.name}
						</option>
					))}
				</Select>
				{errors?.billing?.country && (
					<FormErrorMessage>
						{errors?.billing?.country.message}
					</FormErrorMessage>
				)}
			</FormControl>

			<FormControl isInvalid={!!errors?.billing?.state}>
				<FormLabel>{__('State', 'masteriyo')}</FormLabel>
				<Select {...register('billing.state')} defaultValue={state}>
					{!isEmpty(matchCountriesData) ? (
						matchCountriesData[0].states.map(
							(stateData: { code: string; name: string }) => (
								<option value={stateData.code} key={stateData.code}>
									{stateData.name}
								</option>
							)
						)
					) : (
						<option>{__('No state found', 'masteriyo')}</option>
					)}
				</Select>
				{errors?.billing?.state && (
					<FormErrorMessage>{errors?.billing.state.message}</FormErrorMessage>
				)}
			</FormControl>
		</>
	);
};

export default CountryState;
