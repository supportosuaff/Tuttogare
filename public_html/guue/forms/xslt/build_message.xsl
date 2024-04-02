<?xml version="1.0" encoding="UTF-8"?>
<!-- 
####################################################################################
#  XSL name : build_message
#  Version : R2.0.9.S03                                        
#  Intermediate release number : 022-20180608                                     
#  Last update : 14/04/2016                                                            
####################################################################################
 -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0">
	<xsl:variable name="rules" select="document('./../validation_rule.xml')"/>
	<xsl:template name="msg">
		<xsl:param name="rule"/>
		<xsl:param name="content"/>
		<xsl:if test="$rules//*[@RULE=$rule]">
			<xsl:element name="{$rules//*[@RULE=$rule]/@*[local-name()=$weight]}">
				<xsl:attribute name="RULE" select="$rule"/>
				<xsl:if test="$content!=''">
					<xsl:value-of select="$content"/>
					<xsl:text>: </xsl:text>
				</xsl:if>
				<xsl:value-of select="$rules//*[@RULE=$rule]"/>
			</xsl:element>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
