/*
** Zabbix
** Copyright (C) 2001-2020 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

package oracle

import (
	"context"
	"fmt"
)

const keyDataFiles = "oracle.datafiles.stats"

const datafilesMaxParams = 0

// DataFileHandler TODO: add description.
func DataFileHandler(ctx context.Context, conn OraClient, params []string) (interface{}, error) {
	var datafiles string

	if len(params) > datafilesMaxParams {
		return nil, errorTooManyParameters
	}

	row, err := conn.QueryRow(ctx, `
		SELECT
			JSON_OBJECT('datafile_num' VALUE COUNT(*))
		FROM
			V$DATAFILE
	`)
	if err != nil {
		return nil, fmt.Errorf("%w (%s)", errorCannotFetchData, err.Error())
	}

	err = row.Scan(&datafiles)
	if err != nil {
		return nil, fmt.Errorf("%w (%s)", errorCannotParseData, err.Error())
	}

	return datafiles, nil
}
