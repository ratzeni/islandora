<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:fractions="http://vre.upei.ca/fractions/">
 <xsl:output method="xml" omit-xml-declaration="yes"/>
 <!-- FRACTION XSLT -->
 <xsl:template match="/">

<h4><xsl:value-of select="/fractions:sample/fractions:identifier"/></h4>
<table cellpadding="8">
	<tr><td><b>Plate:</b></td><td><xsl:value-of select="/fractions:sample/fractions:plate"/></td></tr>
	<tr><td><strong>Weight:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:weight"/></td></tr>

	<xsl:for-each select="/fractions:sample/fractions:ptp1b">
	<tr>
		<xsl:choose>
				<xsl:when test = "//fractions:ptp1b = 'Hit'">
				<td><strong>PTP1B:</strong></td>	
		          <td bgcolor="red">
		          <xsl:value-of select="/fractions:sample/fractions:ptp1b"/></td>
				<td><strong> Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:ptp1b_com"/></td>
		        </xsl:when>
		        
		        <xsl:when test = "//fractions:ptp1b = 'Strong'">
				<td><strong>PTP1B:</strong></td>	
		          <td bgcolor="yellow">
		          <xsl:value-of select="/fractions:sample/fractions:ptp1b"/></td>
				<td><strong> Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:ptp1b_com"/></td>
		        </xsl:when>
		        <xsl:when test = "//fractions:ptp1b = 'Medium'">
				<td><strong>PTP1B:</strong></td>	
		          <td bgcolor="orange">
		          <xsl:value-of select="/fractions:sample/fractions:ptp1b"/></td>
				<td><strong> Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:ptp1b_com"/></td>
		        </xsl:when>
		        <xsl:when test = "//fractions:ptp1b = 'Low'">
				<td><strong>PTP1B:</strong></td>	
		          <td bgcolor="grey">
		          <xsl:value-of select="/fractions:sample/fractions:ptp1b"/></td>
				<td><strong> Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:ptp1b_com"/></td>
		        </xsl:when>
		        <xsl:otherwise>
				<td><strong>PTP1B:</strong></td>	
	          <td><xsl:value-of select="/fractions:sample/fractions:ptp1b"/></td>
				<td><strong> Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:ptp1b_com"/></td>
		        </xsl:otherwise>
		      </xsl:choose>
	</tr>
		</xsl:for-each>
	<xsl:for-each select="/fractions:sample/fractions:hct116">
		<tr>
			<xsl:choose>
			        <xsl:when test = "//fractions:hct116 = 'Hit'">
					<td><strong>HCT116:</strong></td>	
			          <td bgcolor="red">
			          <xsl:value-of select="/fractions:sample/fractions:hct116"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hct116_com"/></td>
			        </xsl:when>

			        <xsl:when test = "//fractions:hct116 = 'Strong'">
					<td><strong>HCT116:</strong></td>	
			          <td bgcolor="yellow">
			          <xsl:value-of select="/fractions:sample/fractions:hct116"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hct116_com"/></td>
			        </xsl:when>
			        <xsl:when test = "//fractions:hct116 = 'Medium'">
					<td><strong>HCT116:</strong></td>	
			          <td bgcolor="orange">
			          <xsl:value-of select="/fractions:sample/fractions:hct116"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hct116_com"/></td>
			        </xsl:when>
			        <xsl:when test = "//fractions:hct116 = 'Low'">
					<td><strong>HCT116:</strong></td>	
			          <td bgcolor="grey">
			          <xsl:value-of select="/fractions:sample/fractions:hct116"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hct116_com"/></td>
			        </xsl:when>
			        <xsl:otherwise>
					<td><strong>HCT116:</strong></td>	
		          <td><xsl:value-of select="/fractions:sample/fractions:hct116"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hct116_com"/></td>
			        </xsl:otherwise>
			      </xsl:choose>
		</tr>
			</xsl:for-each>
	<xsl:for-each select="/fractions:sample/fractions:hela">
		<tr>
			<xsl:choose>
			        <xsl:when test = "//fractions:hela = 'Hit'">
					<td><strong>HELA:</strong></td>	
			          <td bgcolor="red">
			          <xsl:value-of select="/fractions:sample/fractions:hela"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hela_com"/></td>
			        </xsl:when>
			
			        <xsl:when test = "//fractions:hela = 'Strong'">
					<td><strong>HELA:</strong></td>	
			          <td bgcolor="yellow">
			          <xsl:value-of select="/fractions:sample/fractions:hela"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hela_com"/></td>
			        </xsl:when>
			        <xsl:when test = "//fractions:hela = 'Medium'">
					<td><strong>HELA:</strong></td>	
			          <td bgcolor="orange">
			          <xsl:value-of select="/fractions:sample/fractions:hela"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hela_com"/></td>
			        </xsl:when>
			        <xsl:when test = "//fractions:hela = 'Low'">
					<td><strong>HELA:</strong></td>	
			          <td bgcolor="grey">
			          <xsl:value-of select="/fractions:sample/fractions:hela"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hela_com"/></td>
			        </xsl:when>
			        <xsl:otherwise>
					<td><strong>HELA:</strong></td>	
		          <td><xsl:value-of select="/fractions:sample/fractions:hela"/></td>
					<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:hela_com"/></td>
			        </xsl:otherwise>
			      </xsl:choose>
		</tr>
			</xsl:for-each>
			<xsl:for-each select="/fractions:sample/fractions:pc3">
				<tr>
					<xsl:choose>
					        <xsl:when test = "//fractions:pc3 = 'Hit'">
							<td><strong>PC3:</strong></td>	
					          <td bgcolor="red">
					          <xsl:value-of select="/fractions:sample/fractions:pc3"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:pc3_com"/></td>
					        </xsl:when>

					        <xsl:when test = "//fractions:pc3 = 'Strong'">
							<td><strong>PC3:</strong></td>	
					          <td bgcolor="yellow">
					          <xsl:value-of select="/fractions:sample/fractions:pc3"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:pc3_com"/></td>
					        </xsl:when>
					        <xsl:when test = "//fractions:pc3 = 'Medium'">
							<td><strong>PC3:</strong></td>	
					          <td bgcolor="orange">
					          <xsl:value-of select="/fractions:sample/fractions:pc3"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:pc3_com"/></td>
					        </xsl:when>
					        <xsl:when test = "//fractions:pc3 = 'Low'">
							<td><strong>PC3:</strong></td>	
					          <td bgcolor="grey">
					          <xsl:value-of select="/fractions:sample/fractions:pc3"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:pc3_com"/></td>
					        </xsl:when>
					        <xsl:otherwise>
							<td><strong>PC3:</strong></td>	
				          <td><xsl:value-of select="/fractions:sample/fractions:pc3"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:pc3_com"/></td>
					        </xsl:otherwise>
					      </xsl:choose>
				</tr>
					</xsl:for-each>
					
			<xsl:for-each select="/fractions:sample/fractions:are">
				<tr>
					<xsl:choose>
					        <xsl:when test = "//fractions:are = 'Hit'">
							<td><strong>ARE:</strong></td>	
					          <td bgcolor="red">
					          <xsl:value-of select="/fractions:sample/fractions:are"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:are_com"/></td>
					        </xsl:when>

					        <xsl:when test = "//fractions:are = 'Strong'">
							<td><strong>ARE:</strong></td>	
					          <td bgcolor="yellow">
					          <xsl:value-of select="/fractions:sample/fractions:are"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:are_com"/></td>
					        </xsl:when>
					        <xsl:when test = "//fractions:are = 'Medium'">
							<td><strong>ARE:</strong></td>	
					          <td bgcolor="orange">
					          <xsl:value-of select="/fractions:sample/fractions:are"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:are_com"/></td>
					        </xsl:when>
					        <xsl:when test = "//fractions:are = 'Low'">
							<td><strong>ARE:</strong></td>	
					          <td bgcolor="grey">
					          <xsl:value-of select="/fractions:sample/fractions:are"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:are_com"/></td>
					        </xsl:when>
					        <xsl:otherwise>
							<td><strong>ARE:</strong></td>	
				          <td><xsl:value-of select="/fractions:sample/fractions:are"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:are_com"/></td>
					        </xsl:otherwise>
					      </xsl:choose>
				</tr>
			</xsl:for-each>
			<xsl:for-each select="/fractions:sample/fractions:antiproliferative">
				<tr>
					<xsl:choose>
					        <xsl:when test = "//fractions:antiproliferative = 'Hit'">
							<td><strong>Antiproliferative:</strong></td>	
					          <td bgcolor="red">
					          <xsl:value-of select="/fractions:sample/fractions:antiproliferative"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:antiproliferative_com"/></td>
					        </xsl:when>

					        <xsl:when test = "//fractions:antiproliferative = 'Strong'">
							<td><strong>Antiproliferative:</strong></td>	
					          <td bgcolor="yellow">
					          <xsl:value-of select="/fractions:sample/fractions:antiproliferative"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:antiproliferative_com"/></td>
					        </xsl:when>
					        <xsl:when test = "//fractions:antiproliferative = 'Medium'">
							<td><strong>Antiproliferative:</strong></td>	
					          <td bgcolor="orange">
					          <xsl:value-of select="/fractions:sample/fractions:antiproliferative"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:antiproliferative_com"/></td>
					        </xsl:when>
					        <xsl:when test = "//fractions:antiproliferative = 'Low'">
							<td><strong>Antiproliferative:</strong></td>	
					          <td bgcolor="grey">
					          <xsl:value-of select="/fractions:sample/fractions:antiproliferative"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:antiproliferative_com"/></td>
					        </xsl:when>
					        <xsl:otherwise>
							<td><strong>Antiproliferative:</strong></td>	
				          <td><xsl:value-of select="/fractions:sample/fractions:antiproliferative"/></td>
							<td><strong>Comments:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:antiproliferative_com"/></td>
					        </xsl:otherwise>
					      </xsl:choose>
				</tr>
					</xsl:for-each>
			<tr><td><strong>Location:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:location"/></td></tr>		
			<tr><td><strong>Notes:</strong></td><td><xsl:value-of select="/fractions:sample/fractions:notes"/></td></tr>		

</table>	
 </xsl:template>
</xsl:stylesheet>
