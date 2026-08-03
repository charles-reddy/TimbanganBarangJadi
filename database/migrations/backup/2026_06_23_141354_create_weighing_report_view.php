<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW v_all_weighing_transactions AS
            
            -- Single product transactions (dari trscale)
            SELECT 
                'SINGLE' as trans_type,
                t.id,
                NULL as trans_no,
                t.driver,
                t.carID,
                t.custID,
                c.custName,
                t.transpID,
                tr.transpName,
                t.doNo,
                t.poNo,
                t.itemCode,
                p.itemName,
                1 as product_count,
                0 as total_karung,
                t.timbangin as tare_weight,
                t.timbangout as gross_weight,
                t.netto as net_weight,
                0.00 as theoretical_weight,
                NULL as correction_factor,
                ISNULL(t.avgkarung, 0.00) as avg_per_karung,
                t.jam_in as weigh_in_time,
                t.jam_out as weigh_out_time,
                NULL as user_in_id,
                NULL as user_in_name,
                NULL as user_out_id,
                NULL as user_out_name,
                'COMPLETED' as status,
                CAST(0 as BIT) as need_approval,
                NULL as approved_by,
                NULL as approved_at,
                t.remarks,
                t.created_at,
                t.updated_at
            FROM trscale t
            LEFT JOIN customers c ON c.custID = t.custID
            LEFT JOIN transporters tr ON tr.transpID = t.transpID
            LEFT JOIN products p ON p.itemCode = t.itemCode
            WHERE t.timbangout IS NOT NULL AND t.timbangout > 0
            
            UNION ALL
            
            -- Multi product transactions (dari trscale_headers + details)
            SELECT 
                'MULTI' as trans_type,
                h.id,
                h.trans_no,
                h.driver,
                h.carID,
                h.custID,
                h.custName,
                h.transpID,
                h.transpName,
                h.doNo,
                h.poNo,
                STUFF((SELECT ', ' + d2.itemCode 
                       FROM trscale_details d2 
                       WHERE d2.header_id = h.id 
                       FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '') as itemCode,
                STUFF((SELECT ', ' + d2.itemName 
                       FROM trscale_details d2 
                       WHERE d2.header_id = h.id 
                       FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '') as itemName,
                COUNT(DISTINCT d.id) as product_count,
                SUM(d.qty_karung) as total_karung,
                h.tare_weight,
                h.gross_weight,
                h.net_weight,
                h.theoretical_weight,
                h.correction_factor,
                AVG(d.avg_per_karung) as avg_per_karung,
                h.weigh_in_time,
                h.weigh_out_time,
                h.user_in_id,
                (SELECT TOP 1 name FROM users WHERE id = h.user_in_id) as user_in_name,
                h.user_out_id,
                (SELECT TOP 1 name FROM users WHERE id = h.user_out_id) as user_out_name,
                h.status,
                h.need_approval,
                h.approved_by,
                h.approved_at,
                h.remarks,
                h.created_at,
                h.updated_at
            FROM trscale_headers h
            LEFT JOIN trscale_details d ON d.header_id = h.id
            WHERE h.status IN ('COMPLETED', 'APPROVED')
            GROUP BY h.id, h.trans_no, h.driver, h.carID, h.custID, h.custName, 
                     h.transpID, h.transpName, h.doNo, h.poNo, h.tare_weight, 
                     h.gross_weight, h.net_weight, h.theoretical_weight, 
                     h.correction_factor, h.weigh_in_time, h.weigh_out_time, 
                     h.user_in_id, h.user_out_id, h.status, h.need_approval, 
                     h.approved_by, h.approved_at, h.remarks, h.created_at, h.updated_at
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_all_weighing_transactions");
    }
};
